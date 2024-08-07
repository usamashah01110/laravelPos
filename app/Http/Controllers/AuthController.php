<?php

namespace App\Http\Controllers;


use App\Helpers\ApiResponseHelper;
use App\Jobs\RefreshTokensJob;
use App\Models\Clinics;
use App\Models\Oauth;
use App\Models\PasswordReset;
use App\Models\Team;
use App\Models\UserCode;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use BaconQrCode\Encoder\QrCode;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\ImageRendererInterface;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Carbon\Carbon;
use App\Notifications\UserLoggedIn; // Import the UserLoggedIn notification if not already imported.
use App\Notifications\UserRegistered;
use App\Notifications\UserVerified;
use App\Notifications\UserLoggedOut;
use App\Notifications\SentOtpSms;
use App\Notifications\SentVerifyEmail;


//use http\Url;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Twilio\Rest\Client;

use App\Models\User;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Twilio\Rest\Oauth\V1\TokenPage;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use function PHPUnit\Framework\stringEndsWith;

use OTPHP\TOTP;
use Illuminate\Support\Facades\Http;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    private $userRepository;
    public function __construct(User $user,BaseRepositoryInterface $userRepository)
    {
        $this->userRepository= $userRepository;
        $this->user = $user;
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register User",
     *     operationId="registerUser",
     *      tags={"Register"},
     *      summary="Register a new user",
     *      description="Registers a new user in the system.",
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         description="User's first_name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
     *         in="query",
     *         description="User's last_name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         description="User's phone number",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *      @OA\Response(response="202", description="Accepted"),
     *      @OA\Response(response="208", description="AlreadyReported"),
     *      @OA\Response(response="400", description="BadRequest"),
     *      @OA\Response(response="401", description="Invalid credentials"),
     *      @OA\Response(response="403", description="Forbidden"),
     *      @OA\Response(response="404", description="NotFound"),
     *      @OA\Response(response="406", description="NotAcceptable"),
     *      @OA\Response(response="422", description="UnprocessableEntity"),
     *      @OA\Response(response="500", description="InternalServerError")
     * )
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'email' => 'required|string|email|max:255|unique:users|regex:/@.*\./',
            'password' => 'required|string|min:6|max:255',
            'role' => 'required|string|max:255',
            'phone' => 'required|unique:users|regex:/^([0-9\s\-\+\(\)]*)$/|min:12',
        ], [
            'first_name.required' => 'First name required',
            'last_name.required' => 'Last name required',
        ]);
        if ($validator->fails()) {
            return ApiResponseHelper::sendErrorResponse($validator->errors(), 'Validation Error', 500);
        }
        try {
            $user = User::updateOrCreate(
                ['email' => $request['email']],
                [
                    'first_name' => $request['first_name'],
                    'last_name' => $request['last_name'],
                    'email' => $request['email'],
                    'password' => bcrypt($request['password']),
                    'phone' => $request['phone'],
                    'role' => $request['role'],
                ]
            );
            $token = Auth::login($user); // this authenticates the user details with the database and generates a token
            if (!$token) {
                return ApiResponseHelper::sendErrorResponse([], "invalid token credentials", 400);
            }
        } catch (JWTException $e) {
            return ApiResponseHelper::sendErrorResponse([], $e->getMessage(), 500);
        }
        $response = [];
        $er_emailPhone = $this->emailPhoneVerification();
        if ($er_emailPhone->getData()) {
            $response['msg'] = $er_emailPhone->getData();
        }
        $response['user'] = $user;
        $response['access_token'] = $token;
        $response['expires_in'] = auth('api')->factory()->getTTL() * 60;    // get token expires in seconds;
//      return response()->json($response);
        $user->notify(new UserRegistered($user));


        if($user->role == 'business_owner')
        {
            $register_clinic = new Clinics();
            $register_clinic->owner_id = $user->id;
            $register_clinic->save();
            return ApiResponseHelper::sendSuccessResponse($response, "Clinic Registered Successfully", 200);

        } else if($user->role == 'staff') {
            $register_team = new Team();
            $register_team->owner_id = '17';
            $register_team->service_id = '3';
            $register_team->save();
            return ApiResponseHelper::sendSuccessResponse($response, "Team Member Registered Successfully", 200);
        }else{
            return ApiResponseHelper::sendSuccessResponse($response, "User Registered Successfully", 200);
        }
    }

    public function emailPhoneVerification()
    {
        $request = app('request');
        $email = $this->verifyEmail($request);
        $phone = $this->sendOtp($request,'');
        $response = [];
        if ($email->status() == 500) {
            $response['email'] = $email->getData()->msg;
        }
        if ($email->status() == 200) {
            $response['email'] = $email->getData()->msg;
        }
        if ($phone->status() == 200) {
            $response['phone'] = $phone->getData()->msg;
        }
        if ($phone->status() == 500) {
            $response['phone'] = $phone->getData()->msg;
        }
        return response()->json($response);
    }

    /**
     * @OA\Post(
     *     path="/resend",
     *     summary="Activate User Account",
     *     tags={"Resend otp and code"},
     *
     *          @OA\Parameter(
     *          name="email",
     *          in="query",
     *          description="User's email",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Parameter(
     *          name="phone",
     *          in="query",
     *          description="User's phone",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Response(
     *         response=202,
     *         description="Activation successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Your email and Phone number are verified.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=423,
     *         description="Activation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", example={"email": {"email activation error message"}, "phone": {"Phone activation error message"}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", example={"code": {"Code validation error message"}, "otp": {"OTP validation error message"}, "email": {"email validation error message"}})
     *         )
     *     )
     * )
     */

    public function resend(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:12',
        ]);
        if ($validator->fails()) {
            return ApiResponseHelper::sendErrorResponse($validator->errors(), 'Validation Error', 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user){
            if ($user->email_verified == 1 && $user->phone_verified == 1 && $user->status == 'activate') {
                $response = ["message"=>"your email and Phone number are already verified."];
                return ApiResponseHelper::sendSuccessResponse($response, "", 208);
            }else{
                // Both email and phone are verified, proceed with the request
                $this->emailPhoneVerification();
                $response = ["message"=>"Verification credentials resend successfully."];
                return ApiResponseHelper::sendSuccessResponse($response, "", 200);
            }
        }else{
            return \response()->json(['message'=>'user not found.']);
        }
    }

    public function beforeVerifyEmail(Request $request) // for ('/verify_email') route
    {
        $user = User::where('email', $request->email)->get();
        if (count($user) > 0) {
            return ApiResponseHelper::sendSuccessResponse($request->email, $this->verifyEmail($request), 200);
        } else {
            return ApiResponseHelper::sendErrorResponse([], 'User not found.', 404);
        }
    }

    public function verifyEmail(Request $request)
    {
        try {
            $code = rand(100000, 999999);
            $data['code'] = $code;
            $data['email'] = $request->email;
            $data['time'] = Carbon::now()->format('Y-m-d H:i:s');
            $data['codeExpiry'] = Carbon::now()->addMinutes(5)->format('Y-m-d H:i:s');
            $data['title'] = "email Verification Code";
            $data['body'] = "This is email Verification Code. Please use this code for verification.";
            UserCode::updateOrCreate(
                ['email' => $request->email],
                [
                    'email' => $data['email'],
                    'code' => $data['code'],
                    'expired_at' => $data['codeExpiry'],
                    'created_at' => $data['time']
                ]);
            Mail::send('verifyMail', ['data' => $data], function ($message) use ($data) {
                $message->to($data['email'])->subject($data['title']);
            });
            $response['message'] = "Please check your email to verification.";
            $user = User::where('email', $request->email)->first();
            $data['user_id'] = $user->id;
            $user->notify(new SentVerifyEmail( $data));
            return ApiResponseHelper::sendSuccessResponse($response, "Please check your email to verification.", 200);

        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], $e->getMessage(), 500);
        }
    }

    public function beforeSendOtp(Request $request) // for ('/send-otp') route
    {
        $user = User::where('phone', $request->phone)->first();
        if ($user) {
            return $this->sendOtp($request,$user);
        } else {
            return ApiResponseHelper::sendErrorResponse([], 'Phone not found.', 404);
        }
    }

    public function sendOtp(Request $request,$user = null)
    {
        try {
            // Generate a random OTP
            $otp = mt_rand(1000, 9999);
            // Send the OTP using Twilio
            $data['phone'] = $request->phone;
            $data['otp'] = $otp;
            $codeExpiry = Carbon::now()->addMinutes(5)->format('Y-m-d H:i:s');
            $time = Carbon::now()->format('Y-m-d H:i:s');
            UserCode::updateOrCreate([
                'phone' => $request->phone,
            ], [
                'phone' => $request->phone,
                'otp' => $otp,
                'expired_at' => $codeExpiry,
                'created_at' => $time
            ]);
            $user = User::where('phone', $request->phone)->first();
            $user->notify(new SentOtpSms($user));
            $phone_req = $request->input('phone');
            $phone = '+' . $phone_req;
            $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            $message = $twilio->messages->create(
                $phone, // User's phone number passed in the request
                [
                    'from' => config('services.twilio.from'),
                    'body' => "Your confirmation OTP is: $otp",
                ]
            );
            $response = "Please check your phone to verification. Your confirmation OTP is: $otp";

            return ApiResponseHelper::sendSuccessResponse($response, "Otp Sent Succesfully", 200);

        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], $e->getMessage(), 500);
        }
    }


    public function activation(Request $request)
    {
        $check = true;
        $emailActivation = $this->verifyCode($request, $check);
        $phoneActivation = $this->checkOtp($request, $check);

        $emailStatus = $emailActivation->status();
        $phoneStatus = $phoneActivation->status();

        $response = [];

        if ($emailStatus == 202 && $phoneStatus == 202) {
            $userCode = UserCode::where('code', $request->code)->first();
            $userotp = UserCode::where('otp', $request->otp)->first();
            $userCode->delete();
            $userotp->delete();
           $user = User::updateOrCreate([
                'email' => $request->email
            ], [
                'status' => 'activate',
                'email_verified' => true,
                'phone_verified' => true
            ]);

        // Send the UserVerified notification to the user
        $user->notify(new UserVerified($user));
            $message = 'your Email and Phone number are verified.';
            return ApiResponseHelper::sendSuccessResponse(null,$message, 200);
        }

        $response['email'] = $emailActivation->status() == 422 ? ($emailActivation->original['errors'] ?? $emailActivation->original) : $emailActivation->original;
        $response['phone'] = $phoneActivation->status() == 422 ? ($phoneActivation->original['errors'] ?? $phoneActivation->original) : $phoneActivation->original;

        $errorResponse = [];
        $errorSubResponse = [];
        foreach ($response as $item) {
            foreach ($item as $key => $value) {
                $errorSubResponse[$key] = $value;
            }
            $errorResponse['errors'] = $errorSubResponse;
        }

        return ApiResponseHelper::sendErrorResponse($errorResponse, 'Activation Error', 423);
    }

public function verifyCode(Request $request, $check = null)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|string',
            'code' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::sendErrorResponse($validator->errors(), 'Validation error', 422);
        }

        try {
            $user = User::where('email', $request->email)->first();
            $codeTB = UserCode::where('code', $request->code)->first();

            if ($user) {
                if ($user->email_verified == 0) {
                    if ($codeTB && count((array)$user) > 0) {
                        if ($codeTB->code == $request->code) {
                            $codeExpiry = $codeTB->expired_at;

                            if ($codeExpiry > now()) {
                                if ($check == true) {
                                    return ApiResponseHelper::sendSuccessResponse(['email' => 'Your email credentials are valid'], 'Verification successful', 202);
                                } else {
                                    $user->email_verified = true;
                                    $user->save();
                                    $codeTB->delete();

                                    return ApiResponseHelper::sendSuccessResponse(['email' => 'Your email is verified.'], 'Email verified successfully', 202);
                                }
                            } else {
                                return ApiResponseHelper::sendErrorResponse(['email' => 'Your code has expired'], 'Code expired', 406);
                            }
                        } else {
                            return ApiResponseHelper::sendErrorResponse(['email' => 'Your code is invalid'], 'Invalid code', 400);
                        }
                    } else {
                        return ApiResponseHelper::sendErrorResponse(['email' => 'Your email or code is invalid.'], 'Invalid email or code', 400);
                    }
                } else {
                    return ApiResponseHelper::sendSuccessResponse(['email' => 'Your email is already verified.'], 'Email already verified', 208);
                }
            } else {
                return ApiResponseHelper::sendErrorResponse(['email' => 'Email not found.'], 'Email not found', 404);
            }
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], $e->getMessage(), 500);
        }
    }


    public function checkOtp(Request $request, $check = null)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:12',
            'otp' => 'required|string|max:6',
        ]);
        if ($validator->fails()) {
            return ApiResponseHelper::sendErrorResponse($validator->errors(), 'validation error', 422);
        }
        try {
            $user = User::where('phone', $request->phone)->first();
            $otp = UserCode::where('otp', $request->otp)->first();
            if (!empty($user)) {
                if ($user['phone_verified'] == 0) {
                    if (isset($otp) && count((array)$user) > 0) {
                        if ($otp->otp == $request->otp) {
                            $codeExpiry = $otp->expired_at;
                            if ($codeExpiry > now()) {
                                if ($check == true) {
                                    return response()->json(['phone' => 'Your Phone credentials are valid'], 202);
                                } else {
                                    $user->phone_verified = true;
                                    $user->save();
                                    $otp->delete();
                                    return ApiResponseHelper::sendSuccessResponse($request->phone, 'Phone number is verified.' ,200);
                                }
                            } else {
                                return ApiResponseHelper::sendErrorResponse([], 'your code has been expired',406);
                            }
                        } else {
                            return ApiResponseHelper::sendErrorResponse([], 'your OTP is Invalid' ,400);
                        }
                    } else {
                        return ApiResponseHelper::sendErrorResponse([], 'your Phone or OTP are InValid.' ,400);
                    }
                } else {
                    return ApiResponseHelper::sendErrorResponse([], 'your phone is already Verified.' ,208);
                }
            } else {

                return ApiResponseHelper::sendErrorResponse([], 'Phone number not found.' ,404);
            }
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], $e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login user",
     *     operationId="loginUser",
     *      tags={"Login"},
     *      summary="Login new user",
     *      description="Login a new user in the system.",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="password",
     *         in="query",
     *         description="User's password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Login successful"),
     *      @OA\Response(response="202", description="Accepted"),
     *      @OA\Response(response="208", description="AlreadyReported"),
     *      @OA\Response(response="400", description="BadRequest"),
     *      @OA\Response(response="401", description="Invalid credentials"),
     *      @OA\Response(response="403", description="Forbidden"),
     *      @OA\Response(response="404", description="NotFound"),
     *      @OA\Response(response="406", description="NotAcceptable"),
     *      @OA\Response(response="422", description="UnprocessableEntity"),
     *      @OA\Response(response="500", description="InternalServerError")
     * )
     */

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|string',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return ApiResponseHelper::sendErrorResponse($validator->errors(), 'Validation Error', 422);
        }
        // Retrieve the validated input...
        $validated = $validator->validated();
        try {
            // this authenticates the user details with the database and generates a token
            $validated = $validator->safe()->only(['email', 'password']);
            $token = Auth::attempt($validated);
            $user = Auth::user();
            if (!$token) {
                return ApiResponseHelper::sendErrorResponse([], "invalid login credentials", 400);
            }
            $user->notify(new UserLoggedIn($user));
        } catch (JWTException $e) {
            return ApiResponseHelper::sendErrorResponse([], $e->getMessage(), 500);
        }
        $response = [];
        $response['id'] = $user['id'];
        $response['user'] = $user;
        $response['refresh_token'] = auth()
            ->claims([
                'xtype' => 'refresh',
                'xpair' => auth()->payload()->get('jti')
            ])
            ->setTTL(auth('api')->factory()->getTTL() * 3)
            ->tokenById(auth('api')->user()->id);
        $response['access_token'] = $token;
        $response['expires_in'] = auth('api')->factory()->getTTL() * 60 * 24 * 365;    // get token expires in seconds;

        Oauth::updateOrCreate(
            ['user_id' => $user['id']],
            [
                'refresh_token' => $response['refresh_token'],
                'access_token' => $token,
                'expires' => $response['expires_in']
            ]);
        return ApiResponseHelper::sendSuccessResponse($response, 'Login successful.', 200);
    }
    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="User Logout",
     *     tags={"LogOut"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="User successfully logged out",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout successful.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized logout request.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Access denied.")
     *         )
     *     )
     * )
     */

    public function logout()
    {
        // get token
        $token = JWTAuth::getToken();
        $user = Auth::user();
        $currentAuth = Oauth::where('user_id', $user->id)->first();
        $currentAuth->delete();
        $user->notify(new UserLoggedOut($user));
        // invalidate token
        $invalidate = JWTAuth::invalidate($token);

        if ($invalidate) {
            $response = ['message'=>'Successfully logged out'];
            return ApiResponseHelper::sendSuccessResponse($response, 'Successfully logged out', 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/refresh",
     *     summary="Refresh Token",
     *     tags={"Refresh Token"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Token Refresh successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="token refresh successful.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized refresh request.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Access denied.")
     *         )
     *     )
     * )
     */

    public function refresh(Request $request)
    {
        $user = auth()->user();
        $token = JWTAuth::fromUser($user);
        return response()->json([
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/me",
     *     summary="Get User",
     *     tags={"Get Authenticate User"},
     *     security={{ "bearerAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="User get successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user get successful.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized get user request.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Access denied.")
     *         )
     *     )
     * )
     */

    public function getAuthenticatedUser()
    {
        return ApiResponseHelper::sendSuccessResponse(Auth::user(), 'logged In User' ,200);
    }

    /**
     * @OA\Post(
     *     path="/forget_password",
     *     summary="user forget-password",
     *     operationId="forgetPassword",
     *      tags={"Forget Password"},
     *      summary="user forgetPassword",
     *      description="Forget Password in the system.",
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's forget password",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description=" check email."),
     *      @OA\Response(response="202", description="Accepted"),
     *      @OA\Response(response="208", description="AlreadyReported"),
     *      @OA\Response(response="400", description="BadRequest"),
     *      @OA\Response(response="401", description="Invalid credentials"),
     *      @OA\Response(response="403", description="Forbidden"),
     *      @OA\Response(response="404", description="NotFound"),
     *      @OA\Response(response="406", description="NotAcceptable"),
     *      @OA\Response(response="422", description="UnprocessableEntity"),
     *      @OA\Response(response="500", description="InternalServerError")
     * )
     */

    public function forgetPassword(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->get();
            if (count($user) > 0) {
                $token = Str::random(40);
                $domain = URL::to('/');
                $url = $domain . '/reset-password?token=' . $token;
                $data['url'] = $url;
                $data['email'] = $request->email;
                $data['title'] = 'Password Reset';
                $data['body'] = 'Please click on below link to reset your password';

                Mail::send('verifyMail', ['data' => $data], function ($message) use ($data) {
                    $message->to($data['email'])->subject($data['title']);
                });

                $datetime = Carbon::now()->format('Y-m-d H:i:s');
                PasswordReset::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'email' => $request->email,
                        'token' => $token,
                        'created_at' => $datetime
                    ]
                );
//                return response()->json([
//                    'message' => 'Please check your mail to reset your password.'
//                ], 200);
                $response = ['message'=>'Please check your mail to reset your password.'];
                return ApiResponseHelper::sendSuccessResponse($response, "Please check your mail to reset your password.", 200);

            } else {
                return ApiResponseHelper::sendErrorResponse([], 'User not found.', 404);
            }
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], $e->getMessage(), 500);
        }
    }

    public function callBackResetPassword(Request $request)
    {
        $resetData = PasswordReset::where('token', $request->token)->get();
        if (isset($request->token) && count($resetData) > 0) {
            $user = User::where('email', $resetData[0]['email'])->get();
            return view('resetPassword', compact('user'));
        } else {
            return view('404');
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::find($request->id);
        $user->password = Hash::make($request->password);
        $user->save();

        PasswordReset::where('email', $user->email)->delete();
        return '<h1>Your password has been reset successfully. </h1>';
    }

    public function generateQRCode()
    {
        /// get token
        $token = JWTAuth::getToken();

        if (!$token) {
            return ApiResponseHelper::sendErrorResponse([], "invalid login credentials", 400);
        }
        $user = Auth::user();
        $userEmail = $user->email;

        if (!$userEmail) {
            return ApiResponseHelper::sendErrorResponse([], "User email not found.", 404);
        }
        // Create a provisioning URI for the QR code
        $otp = TOTP::generate();
        $otp->setLabel($user->first_name); // Set the label to the App name
        $secretKey = $otp->getSecret();
        if ($secretKey) {
            $user->two_factor_secret = $secretKey;
            $user->save();
        }
        // Get the provisioning URI from your TOTP library
        $provisioningUri = $otp->getProvisioningUri($userEmail);

        // Create the QR code image using the API
        $grCodeUri = $otp->getQrCodeUri(
            'https://api.qrserver.com/v1/create-qr-code/?data=' . urlencode($provisioningUri) . '&size=300x300&ecc=M',
            $provisioningUri
        );

        $response = Http::get($grCodeUri, [
            'size' => 300, // Adjust the size as needed
            'data' => $provisioningUri,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Return the QR code image as a response with the appropriate content type

            return response($response->body(), 200, [
                'Content-Type' => 'image/png',
            ]);
        } else {
            return ApiResponseHelper::sendErrorResponse([], "QR code generation failed", 500);
        }

    }

    public function verifyQrCode(Request $request)
    {
        // Get the user from JWT token
        $user = JWTAuth::user();

        if (!$user) {
            return ApiResponseHelper::sendErrorResponse([], "Invalid login credentials", 400);
        }

        // Get the user's TOTP secret key
        $secretKey = $user->two_factor_secret;

        if (!$secretKey) {
            return ApiResponseHelper::sendErrorResponse([], "TOTP secret key not found for the user", 400);
        }

        // Get the TOTP code entered by the user
        $userEnteredCode = $request->input('code');
        // Create a TOTP instance with the user's secret key
        $otp = TOTP::createFromSecret($secretKey);
        $otp->now();

        // Verify the TOTP code
        if ($otp->verify($userEnteredCode)) {
            $user->qr_verified = true;
            $user->save();
            // TOTP code is valid
            $response = 'OTP code is valid';
            return ApiResponseHelper::sendSuccessResponse($response, "OTP code is valid", 200);
        } else {
            // TOTP code is invalid
            return ApiResponseHelper::sendErrorResponse([], "Invalid OTP code", 400);
        }
    }

    public function redirectToGoogle()
    {
        try {
            $redirectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
            return ApiResponseHelper::sendSuccessResponse(['redirect_url' => $redirectUrl], 'Google redirect URL generated successfully.');
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], $e->getMessage(), 500);
        }
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();

            $response = [
                'first_name' => $user->name,
                'email' => $user->email,
                'google_id' => $user->id,
            ];

            $existingUser = $this->userRepository->existingUser($user->email);
            if (!$existingUser) {
                $user = $this->userRepository->create($response);
            } else {
                $user = $this->userRepository->update($existingUser[0]['id'], $response);
            }

            return ApiResponseHelper::sendSuccessResponse($user, 'Google user authenticated successfully.');
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], 'Authentication failed: ' . $e->getMessage(), 400);
        }
    }

    public function redirectToFacebook()
    {
        try {
            $redirectUrl = Socialite::driver('facebook')->stateless()->redirect()->getTargetUrl();
            return ApiResponseHelper::sendSuccessResponse(['redirect_url' => $redirectUrl], 'Facebook redirect URL generated successfully.');
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], $e->getMessage(), 500);
        }
    }

    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->stateless()->user();

            $response = [
                'first_name' => $user->name,
                'email' => $user->email,
                'facebook_id' => $user->id,
            ];

            $existingUser = $this->userRepository->existingUser($user->email);
            if (!$existingUser) {
                $user = $this->userRepository->create($response);
            } else {
                $user = $this->userRepository->update($existingUser[0]['id'], $response);
            }

            return ApiResponseHelper::sendSuccessResponse($user, 'Facebook user authenticated successfully.');
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], 'Authentication failed: ' . $e->getMessage(), 400);
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::sendErrorResponse($validator->errors(), 'Validation error', 422);
        }

        try {
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->save();
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], 'Failed to update password', 500);
        }

        return ApiResponseHelper::sendSuccessResponse([], 'Password updated successfully',200);
    }


    public function updateClientPassword(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password',
        ]);

        if ($validator->fails()) {
            return ApiResponseHelper::sendErrorResponse($validator->errors(), 'Validation error', 422);
        }

        // Check if the current password is correct
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return ApiResponseHelper::sendErrorResponse([], 'Current password is incorrect', 403);
        }

        // Update the user's password
        try {
            $user = Auth::user();
            $user->password = Hash::make($request->new_password);
            $user->save();
        } catch (\Exception $e) {
            return ApiResponseHelper::sendErrorResponse([], 'Failed to update password', 500);
        }

        return ApiResponseHelper::sendSuccessResponse([], 'Password updated successfully');
    }
}
