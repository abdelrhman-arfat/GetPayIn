<?php

namespace Tests\Feature\Product;

use App\Domain\Services\RedisService;
use App\Models\Product;
use App\Repositories\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;

    public function setUp(): void
    {
        parent::setUp();

        // Create 5 fake products
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

    public function test_returns_product_list()
    {
        $products = Product::all();
        $this->assertCount(5, $products);
    }

    public function test_returns_single_product()
    {
        $product = Product::first();
        $id = $product->id;
        $response = $this->get('/api/products/' . $product->id);
        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertEquals($id, $data['id']);;
    }
}
