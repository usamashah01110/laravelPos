<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
class BulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function testBulkDeleteSuccess()
    {
        $users = User::factory(3)->create();
        $recordIds = $users->pluck('id')->toArray();
        $user = User::factory()->create(['role' => 'admin']);
        $token = Auth::login($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/admin/bulk-delete', [
            'record_ids' => $recordIds,
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Bulk soft delete successful',
        ]);
        foreach ($users as $user) {
            $this->assertSoftDeleted('users', ['id' => $user->id]);
        }
    }

    public function testBulkDeleteValidationFailure()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = Auth::login($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('PUT', '/api/v1/admin/bulk-delete', [
            'record_ids' => [999],
        ]);
        $response->assertStatus(400);

    }
}
