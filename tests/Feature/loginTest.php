<?php
//use Illuminate\Foundation\Testing\RefreshDatabase;
//use Illuminate\Foundation\Testing\WithFaker;
//use Tests\TestCase;
//use App\Models\User;
//
//class loginTest extends TestCase
//{
//    use RefreshDatabase; // Refresh the database before and after the test.
//    use WithFaker; // Use Faker for generating test data.
//
//    public function testUserCannotLoginWithEmptyFields()
//    {
//        // Create a user with known credentials in the database.
//        $user = User::factory()->create([
//            'last_name' => 'john',
//            'phone' => '112233',
//            'email' => 'johndoe@example.com',
//            'password' => bcrypt('password123'),
//        ]);
//
//        // Make an HTTP POST request to the login route with valid credentials.
//        $response = $this->post('/api/v1/login', [
//            'email' => 'ttyy',
//            'password' => '123',
//        ]);
//
//        $response->assertStatus(200)->assertJsonValidationErrors(['email', 'password']);
////        $response->assertStatus(200);
////        $user = User::factory()->create([
////            'last_name' => 'example.com',
////            'phone' => '112233',
////            'email' => 'johndoe@example.com',
////            'password' => bcrypt('password123'),
////        ]);
////        $userData = [
////            'first_name' => $this->faker->firstName,
////            'last_name' => $this->faker->lastName,
////            'email' => $this->faker->unique()->safeEmail,
////            'password' => 'password123',
////            'phone' => $this->faker->phoneNumber,
////        ];
////
////        $this->postJson('/api/v1/register', $userData);
////        $this->assertDatabaseHas('users', [
////            'email' => $userData['email'],
////        ]);
////        $validate = [
////            'email'=>'',
////            'password'=>''
////        ];
////
////        // Make an HTTP POST request to the login route with empty email and password fields.
////        $response = $this->postJson('/api/v1/login', $validate);
////
////        // Assert that the response status code is 422 (indicating validation errors).
////        $response->assertStatus(422)->assertJsonValidationErrors(['email', 'password']);
//
//        // Assert that the response contains validation error messages for 'email' and 'password' fields.
////        $response->assertJsonValidationErrors(['email', 'password']);
//    }
//
//
//    public function testUserCanLoginWithValidCredentials()
//    {
//        // Create a user with known credentials in the database.
//        $user = User::factory()->create([
//            'last_name' => 'john',
//            'phone' => '112233',
//            'email' => 'johndoe@example.com',
//            'password' => bcrypt('password123'),
//        ]);
//
//        // Make an HTTP POST request to the login route with valid credentials.
//        $response = $this->post('/api/v1/login', [
//            'email' => 'johndoe@example.com',
//            'password' => 'password123',
//        ]);
//
//        $response->assertStatus(200);
//    }
//}
