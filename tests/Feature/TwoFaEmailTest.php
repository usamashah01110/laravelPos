<?php
//
//namespace Tests\Feature;
//
//use Illuminate\Foundation\Testing\RefreshDatabase;
//use Illuminate\Foundation\Testing\WithFaker;
//use Tests\TestCase;
//use App\Models\User;
//
//
//class TwoFaEmailTest extends TestCase
//{
//    use RefreshDatabase;
//    use WithFaker;
//    public function testSuccessfulEmailSentTest()
//    {
//        $user = User::factory()->create([
//            'last_name' => 'example.com',
//            'phone' => '112233',
//            'email' => 'johndoe@example.com',
//            'password' => bcrypt('password123'),
//        ]);
//
//        $response = $this->post('/api/v1/verify_email', [
//            'email' => 'johndoe@example.com',
//        ]);
//
//
//        $response->assertStatus(200)
//        ->assertJson(['success'=>true,
//            'message'=>'Please check your email to verification.']);
//    }
//
//    public function testEmptyEmailTest()
//    {
//        $user = User::factory()->create([
//            'last_name' => 'example.com',
//            'phone' => '112233',
//            'email' => 'johndoe@example.com',
//            'password' => bcrypt('password123'),
//        ]);
//
//        $response = $this->post('/api/verify_email', [
//            'email' => '',
//        ]);
//
//
//        $response->assertStatus(200)
//            ->assertJson(['success'=>false,
//                'message'=>'User not found.']);
//    }
//}
