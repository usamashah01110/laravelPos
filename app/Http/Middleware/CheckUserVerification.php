<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponseHelper;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CheckUserVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

//        $validator = Validator::make($request->all(), [
//            'email' => 'required|email|string',
//            'password' => 'required|string|min:6',
//        ]);
//        if ($validator->fails()){
//            return \response()->json($validator->errors());
//        }
        $user = User::where('email', $request->email)->first();
        if ($user){
            if ($user->email_verified == 1 && $user->phone_verified == 1 && $user->status == 'activate') {
                // Both email and phone are verified, proceed with the request
                return $next($request);
            }
            return ApiResponseHelper::sendErrorResponse($user->email, "your email or phone number are not verified", 500);
        }else{
                return ApiResponseHelper::sendErrorResponse([], "user not found.", 500);
            }
        }
}
