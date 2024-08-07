<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function testUpdateUserValidation()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = Auth::login($user);
        $invalidUserData = [
            'first_name' => 'A',
            'last_name' => 'ValidLastName',
            'email' => 'invalid-email',
            'password' => 'short',
            'phone' => '123',
        ];
        $response = $this->json('POST', '/api/v1/admin/user/' . $user->id, $invalidUserData, [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'first_name', 'email', 'password', 'phone',
        ]);
        $response->assertJsonMissing(['last_name']);
        $validator = Validator::make($invalidUserData, [
            'first_name' => 'min:2',
            'email' => 'email',
            'password' => 'min:6',
            'phone' => 'min:10',
        ]);
        if ($validator->passes()) {
            $this->assertDatabaseHas('users', $user->toArray());
        } else {
            $this->assertDatabaseMissing('users', $user->toArray());
        }
    }

}
