<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class ManageRequestTest extends TestCase
{
    use RefreshDatabase;

    public function testNoActionsProvided()
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
        $response = $this->json('POST', '/api/v1/admin/action/manage', [], [
            'Authorization' => 'Bearer ' . $token,]);
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'No actions provided',
        ]);
    }

    public function testSortAction()
    {
        User::factory()->create(['email' => 'user1@example.com', 'first_name' => 'Alice', 'phone' => '123']);
        $user = User::factory()->create(['email' => 'user2@example.com', 'first_name' => 'Bob', 'phone' => '456', 'role' => 'admin']);

        $token = Auth::login($user);
        $response = $this->json('POST', '/api/v1/admin/action/manage', [
            'actions' => [
                [
                    'action' => 'sort',
                    'column' => 'first_name',
                    'order' => 'asc',
                ],
            ],
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(200);

    }

    public function testSearchAction()
    {
        User::factory()->create(['email' => 'user1@example.com', 'first_name' => 'Alice', 'phone' => '123']);
        $user = User::factory()->create(['email' => 'user2@example.com', 'first_name' => 'Bob', 'phone' => '456', 'role' => 'admin']);
        $token = Auth::login($user);

        $response = $this->json('POST', '/api/v1/admin/action/manage', [
            'actions' => [
                [
                    'action' => 'search',
                    'first_name'=>'',
                    'phone'=>'',
                    'email' => 'user1@example.com',
                ],
            ],
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);

    }


    public function testFilterAction()
    {
        User::factory()->create(['role' => 'user', 'status' => 'active']);
        User::factory()->create(['role' => 'admin', 'status' => 'pending']);
        $user =User::factory()->create(['role' => 'user', 'status' => 'active','role' => 'admin']);
        $token = Auth::login($user);
        $response = $this->json('POST', '/api/v1/admin/action/manage', [
            'actions' => [
                [
                    'action' => 'filter',
                    'role' => 'user',
                    'status' => 'active',
                ],
            ],
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(200);

    }

    public function testPaginationAction()
    {
        User::factory()->count(20)->create(['role'=>'admin']);
        $user=User::first();
        $token = Auth::login($user);

        $response = $this->json('POST', '/api/v1/admin/action/manage', [
            'actions' => [
                [
                    'action' => 'pagination',
                    'page' => 2,
                    'pageSize' => 10,
                ],
            ],
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(200);

    }

    public function testBulkEditAction()
    {
        User::factory()->count(5)->create(['role'=>'admin']);
        $user=User::first();
        $token = Auth::login($user);
        $response = $this->json('POST', '/api/v1/admin/action/manage', [
            'actions' => [
                [
                    'action' => 'bulkEdit',
                    'record_ids' => [1, 2, 3],
                    'updates' => ['status' => 'updated'],
                ],
            ],
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
    }

    public function testBulkDeleteAction()
    {
        User::factory()->count(5)->create(['role'=>'admin']);
        $user=User::first();
        $token = Auth::login($user);
        $response = $this->json('POST', '/api/v1/admin/action/manage', [
            'actions' => [
                [
                    'action' => 'bulkdelete',
                    'record_ids' => [1, 2, 3],
                ],
            ],
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);

    }

    public function testInlineEditAction()
    {
        $user = User::factory()->create(['role'=>'admin']);
        $token = Auth::login($user);
        $response = $this->json('POST', '/api/v1/admin/action/manage', [
            'actions' => [
                [
                    'action' => 'inlineEdit',
                    'id' => $user->id,
                    'status' => 'updated',
                ],
            ],
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);

    }

    public function testInvalidAction()
    {
        $user = User::factory()->create(['role'=>'admin']);
        $token = Auth::login($user);
        $response = $this->json('POST', '/api/v1/admin/action/manage', [
            'actions' => [
                [
                    'action' => 'invalidAction',
                ],
            ],
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Invalid action',
        ]);
    }
}
