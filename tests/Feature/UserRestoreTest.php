<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRestoreTest extends TestCase
{
    use RefreshDatabase;

    public function testRestoreExistingUser()
    {
        // from factory make the role admin
        $deletedUser = User::factory()->create();
        $deletedUser->delete();
        $authUser = User::factory()->create(['role' => 'admin']);
        $token = Auth::login($authUser);
        $response = $this->json('PUT', '/api/v1/admin/user/' . $deletedUser->id, [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'User has been restored',
        ]);
    }

    public function testRestoreNonExistentUser()
    {
        $response = $this->json('PUT', '/api/v1/admin/user/restore/999');
        $response->assertStatus(404);
    }
}
