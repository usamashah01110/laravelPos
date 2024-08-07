<?php

namespace Api;

use App\Models\Oauth;
use App\Models\User;
use App\Models\UserCode;
use Faker\Core\Number;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testUserRegistrationValidation()
    {
        $invalidData = [
            'first_name' => '', // Missing first name
            'last_name' => '', // Missing last name
            'email' => '', // Invalid email format
            'password' => '', // Password too short
            'phone' => '', // Invalid phone format
        ];

        $response = $this->postJson('/api/v1/register', $invalidData);

        $response->assertStatus(422)
            ->assertInvalid([
                'first_name', 'last_name', 'email', 'password', 'phone',
            ])->assertJsonMissing($invalidData);
    }

    public function testUserRegistrationSuccess()
    {
        $userData = [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'phone' => '+923024529451',
//            'phone' => '+92'.rand(1000000000,9999999999),
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    // Add other fields you expect in the response
                ],
                'access_token',
                'expires_in',
            ])->assertValid([
                'first_name',
                'last_name',
                'email',
                'phone'
            ]);
        // Optionally, you can assert that a user was created in the database.
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
        return $response->json();
//        dd($response->json(['user'])["phone"]);
//        dd($response->json());
    }

    public function testResendOtpCode(){
        $user = $this->testUserRegistrationSuccess();
        $data = [
            "email"=>$user['user']['email'],
            "phone"=>$user['user']['phone'],
        ];
        $response = $this->postJson('api/v1/resend',$data);
        $response->assertStatus(200)->assertJsonStructure([
            "message"
        ]);
    }

    public function testUserVerification(){
        $user = $this->testUserRegistrationSuccess();
        $vE=UserCode::where('email',$user['user']['email'])->first();
        $vP=UserCode::where('phone',$user['user']['phone'])->first();
        $code = $vE['code'];
        $otp = $vP["otp"];
        $data = [
            "email"=>$user['user']['email'],
            "code"=>"'.$code.'",
            "phone"=>$user['user']['phone'],
            "otp"=>"'.$otp.'"
        ];
        $response = $this->postJson('api/v1/activation',$data);
        $response->assertJsonStructure([
            "message"
        ]);
//        dd($vE['email'],$vE['code'],$vP['phone'],$vP['otp']);
//        dd($response);
    }

    public function testLoginRoute(){
        $user = $this->testUserRegistrationSuccess();
        $validate = [
            'email'=>$user['user']['email'],
            'password'=>'password123'
        ];

        $response = $this->postJson('api/v1/login',$validate);
        $response->assertStatus(200)->assertValid([
            'email','password'
        ]);
//        dd($response);
        return $response->json();
    }

    public function testLogoutRoute()
    {
//        $user = User::factory()->create();
//        $token = JWTAuth::fromUser($user);
//        Auth::login($user); // Simulate authentication
////        // Send a POST request to the logout route with the token.
//        $response = $this->postJson('api/v1/logout', [], ['Authorization' => 'Bearer ' . $token]);
//        // Check the response status code and content.
//        $response->assertStatus(200)->assertJsonStructure([
//                'message',
//            ]);
        // Mock the JWTAuth facade
        $token = 'fake-token'; // Replace with a valid token
        JWTAuth::shouldReceive('getToken')->andReturn($token);
        JWTAuth::shouldReceive('invalidate')->with($token)->andReturn(true);

        // Create a mock user
//        $user = factory(User::class)->create();
        $user = User::factory()->create();;
        Auth::shouldReceive('user')->andReturn($user);

        // Mock the Oauth model and its methods
        $oauthMock = Mockery::mock(Oauth::class);
        $oauthMock->shouldReceive('where')->andReturnSelf();
        $oauthMock->shouldReceive('first')->andReturn($oauthMock);
        $oauthMock->shouldReceive('delete')->andReturn(true);
        $this->app->instance(Oauth::class, $oauthMock);

        // Perform a POST request to the logout endpoint
        $response = $this->postJson('api/v1/logout');

        // Assertions
        $response->assertJsonStructure([
                'message',
            ]);
    }

    public function testRefreshRoute()
    {
        $user = User::factory()->create();
//        dd($user);
        $token = JWTAuth::fromUser($user);

        // Send a POST request to the api/refresh endpoint with the token.
        $response = $this->postJson( 'api/v1/refresh', [], ['Authorization' => 'Bearer ' . $token]);

        // Check the response status code and content.
        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    "id",
                    "first_name",
                    "last_name",
                    "email",
                    "phone",
                    "role",
                    "email_verified",
                    "phone_verified",
                    "status",
                    "verification_token",
                    "reset_token",
                    "reset_token_expires",
                    "password_reset",
                    "created_at",
                    "updated_at"
                ],
                'authorisation' => [
                    'token',
                    'type',
                ],
            ]);
    }

    public function testGetUserData()
    {
        $user = User::factory()->create();
//        $user = $this->testLoginRoute();
        $token = JWTAuth::fromUser($user);
        $response = $this->postJson('api/v1/me', [], ['Authorization' => 'Bearer' . $token]);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    "id",
                    "first_name",
                    "last_name",
                    "email",
                    "phone",
                    "role",
                    "email_verified",
                    "phone_verified",
                    "status",
                    "verification_token",
                    "reset_token",
                    "reset_token_expires",
                    "password_reset",
                    "created_at",
                    "updated_at"
                ],
            ]);
    }
}
