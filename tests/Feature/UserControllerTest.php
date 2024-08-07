<?php

namespace Tests\Feature;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth; // Import the JWTAuth facade
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User; // Import the User model
use Illuminate\Support\Facades\Auth; // Import the Auth facade

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateUser()
    {
        $user = new User([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '1234567890',
            'role'=>'admin',
        ]);
        $user->save();

        $token = Auth::login($user);
        $userData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'janesmith@example.com',
            'password' => 'secret456',
            'phone' => '9876543210',
        ];
        $response = $this->json('POST', '/api/v1/admin/user', $userData, [
            'Authorization' => 'Bearer ' . $token,
        ]);


        $response->assertStatus(200);


        $response->assertJson([
            'message' => 'User Created',

        ]);
    }

}
