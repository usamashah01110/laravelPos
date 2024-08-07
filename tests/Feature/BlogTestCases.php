<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Blog;

class BlogTestCases extends TestCase
{
    use RefreshDatabase; // This trait resets the database after each test

    public function testIndex()
    {
        // Create some blog posts for testing
        Blog::factory()->count(5)->create();

        $response = $this->get('/api/v1/blogs');

        $response->assertStatus(200);

    }

    public function testShowBlog()
    {
        // Create a blog post for testing
        $blogPost = Blog::factory()->create();

        $response = $this->get("api/v1/blogs/{$blogPost->slug}");
        $response->assertStatus(200);
    }

    public function testStore()
    {
        $response = $this->postJson('api/v1/blogs/store',[
            'title' => 'Test1 Blog',
            'meta_title' => 'Test1 Meta Title',
            'meta_description' => 'Test 1 Meta Description',
            'content' => 'Test1 Content',
            'image' => UploadedFile::fake()->image('image1.jpg'),
        ]);

        $response->assertStatus(201);

    }

    public function testUpdate()
    {
        // Create a blog post for testing
        $blogPost = Blog::factory()->create();

        $updatedData = [
            'title' => 'Updated Title',
            'meta_title' => 'Updated Meta Title',
        ];

        $response = $this->post("/api/v1/blogs/{$blogPost->id}", $updatedData);

        $response->assertStatus(200);

    }

    public function testDestroy()
    {
        // Create a blog post for testing
        $blogPost = Blog::factory()->create();

        $response = $this->delete("/api/v1/blogs/delete/{$blogPost->id}");

        $response->assertStatus(200);

    }


    public function testRestoreBlog()
    {
        // Create a blog post and then soft delete it
        $blogPost = Blog::factory()->create();
        $blogPost->delete();

        $response = $this->put("/api/v1/blogs/restore-blog/{$blogPost->id}");

        $response->assertStatus(200);

    }
}
