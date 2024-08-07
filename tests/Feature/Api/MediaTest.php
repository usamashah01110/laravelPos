<?php

namespace Api;


use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class MediaTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_upload_media_file()
    {

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $media = Media::factory()->create();
        $file =$media->getOriginal('filename');
        Storage::fake('uploads');
        $newFile = UploadedFile::fake()->create($file, 1024);
//        dd($newFile);
        $data = [
            'title' => 'Updated Media',
            'file' => $newFile,
        ];
        $response = $this->postJson('/api/v1/media',$data ,['Authorization' => 'Bearer' . $token]);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Your Media file has been uploaded successfully.',
        ]);

    }

    public function test_returns_specific_media_item()
    {
        // Create a sample media record for testing
        $media = Media::factory()->create();
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->getJson("/api/v1/get-media/{$media->id}",['Authorization' => 'Bearer ' . $token]);
        // Assert that the response has a 200 status code
        $response->assertStatus(200);
        // Assert that the response contains the expected media data
        $response->assertJsonStructure([
            'id',
            'title',
            'filename',
        ]);
    }

    public function test_get_all_media()
    {
        Media::factory()->count(3)->create();
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->getJson('api/v1/all-media', ['Authorization' => 'Bearer ' . $token]);
        // Assert that the response has a status code of 200 (OK).
        $response->assertStatus(200);
        // Assert that the response contains the expected JSON structure.
        $response->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'filename',
                    // Add other expected attributes here
                ],
            ],
        ]);
        $response->assertJson(['message' => 'All media successfully loaded.']);
    }


    public function test_update_media_file()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $media = Media::factory()->create();
        Storage::fake('uploads');
        $newFile = UploadedFile::fake()->create('one.jpg', 1024);
        $data = [
            'title' => 'Updated Media',
            'file' => $newFile,
        ];
        $response = $this->postJson("/api/v1/update-media/{$media->id}",$data ,['Authorization' => 'Bearer' . $token]);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Media successfully updated.',
        ]);
    }

    public function test_delete_media_file()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $media = Media::factory()->create();
        $response = $this->postJson("/api/v1/delete-media/{$media->id}",[],['Authorization' => 'Bearer' . $token]);
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Media successfully deleted.',
        ]);
    }

}
