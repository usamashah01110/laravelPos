<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class InlineEditTest extends TestCase
{
    use RefreshDatabase;

    public function testInlineUpdateSuccess()
    {
        $user = User::factory()->create();
        $adminUser = User::factory()->create(['role' => 'admin']);
        $token = Auth::login($adminUser);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/admin/' . $user->id , [
            'status' => 'active',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'User updated successfully',
            'data' => [
                'id' => $user->id,
                'status' => 'active',

            ],
        ]);
    }

    public function testInlineUpdateNoStatusProvided()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);
        $token = Auth::login($adminUser);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/admin/1', []);
        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'No status provided for update',
        ]);
    }

    public function testInlineUpdateUnauthorized()
    {
        $user = User::factory()->create();
        $nonAdminUser = User::factory()->create();
        $token = Auth::login($nonAdminUser);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PATCH', '/api/v1/admin/' . $user->id , [
            'status' => 'active', // Update the user's status
        ]);
        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Unauthorized action.',
        ]);
    }
}
