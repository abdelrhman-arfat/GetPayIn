<?php

namespace Tests\Feature\Product;

use App\Domain\Services\RedisService;
use App\Models\Product;
use App\Repositories\Services\ProductService;
use Illuminate\Foundation\Testing\{
    DatabaseMigrations,
    RefreshDatabase
};
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use DatabaseMigrations;
    private ProductService $productService;

    public function setUp(): void
    {
        parent::setUp();

        parent::setUp();
        Product::factory()->count(5)->create();

        // Mock RedisService
        $mockRedis = $this->createMock(RedisService::class);

        // Define behavior for remember()
        $mockRedis->method('remember')
            ->willReturnCallback(function ($key, $ttl, $callback) {
                return $callback();
            });

        // Inject mock into ProductService
        $this->productService = new ProductService($mockRedis);
    }

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
