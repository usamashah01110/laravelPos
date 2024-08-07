<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User; // Import the User model
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use Illuminate\Foundation\Testing\DatabaseTransactions;
class UserEditTest extends TestCase
{

    use RefreshDatabase;

    public function testEditUserFound()
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '1234567890',
            'role' => 'admin',
        ]);
        $token = Auth::login($user);
        $response = $this->json('GET', '/api/v1/admin/user/' . $user->id, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'johndoe@example.com',
                'phone' => '1234567890',
                'role' => 'admin',
            ],
        ]);
    }

    public function testEditUserNotFound()
    {
         $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => bcrypt('secret123'),
            'phone' => '1234567890',
            'role' => 'admin',
        ]);
        $token = Auth::login($user);
        $this->assertDatabaseMissing('users', ['id' => 999]);
        $response = $this->json('GET', '/api/v1/admin/user/999', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'User not found',
        ]);
    }

}
