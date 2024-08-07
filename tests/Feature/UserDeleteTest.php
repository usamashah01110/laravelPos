<?php
namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function testSoftDeleteUser()
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
        $response = $this->json('DELETE', '/api/v1/admin/' . $user->id, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }

   public function testSoftDeleteNonExistentUser()
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
    $response = $this->json('DELETE', '/api/v1/admin/999', [], [
        'Authorization' => 'Bearer ' . $token,
    ]);
    $response->assertStatus(404);
    }
}
