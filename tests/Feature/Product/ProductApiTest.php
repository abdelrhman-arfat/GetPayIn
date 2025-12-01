<?php

namespace Tests\Feature\Product;

use App\Domain\Interfaces\RedisInterface;
use App\Domain\Services\FakeRedisService;
use App\Domain\Services\RedisService;
use App\Models\Product;
use App\Repositories\Services\ProductService;
use Illuminate\Foundation\Testing\{
    DatabaseMigrations,
    RefreshDatabase
};
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use DatabaseMigrations;

    private ProductService $productService;

    public function setUp(): void
    {
        parent::setUp();

        // Create 5 fake products
        Product::factory()->count(5)->create();

        // Mock RedisService
        app()->bind(RedisInterface::class, FakeRedisService::class);
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
