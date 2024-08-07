<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;
use PHPUnit\Framework\AssertionFailedError;

class ApiResponseTest extends TestCase
{
    /**
     * Test to ensure all API routes have the expected response structure.
     *
     * @return void
     */
    public function testAllApiRoutes()
    {
        // Retrieve all routes
        $routes = Route::getRoutes();

        foreach ($routes as $route) {
            // Check if the route has the 'api' middleware
            if (in_array('api', $route->action['middleware'] ?? [])) {
                $uri = $route->uri();

                // Assuming GET method for testing; adjust as needed for other methods
                if (in_array('GET', $route->methods())) {
                    try {
                        $response = $this->getJson($uri);

                        // You can call a method to assert the structure of each response
                        $this->assertResponseStructure($response);

                    } catch (AssertionFailedError $e) {
                        // Print the failing endpoint and rethrow the exception
                        echo "Test failed for endpoint: $uri\n";
                        throw $e;
                    }
                }
            }
        }
    }

    /**
     * Helper function to assert the response structure.
     *
     * @param \Illuminate\Testing\TestResponse $response
     * @return void
     */
    private function assertResponseStructure($response)
    {
        $response->assertJsonStructure([
            'status',
            'data',
            'msg',
            'errors',
        ]);

        $response->assertStatus(200);
    }
}
