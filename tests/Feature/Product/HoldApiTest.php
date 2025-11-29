<?php

namespace Tests\Feature\Product;

use App\Domain\Services\RedisService;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HoldApiTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Mock RedisService
        $mockRedis = $this->createMock(RedisService::class);
        $mockRedis->method('remember')->willReturnCallback(fn($key, $ttl, $callback) => $callback());

        // Inject mock into ProductService
        $this->productService = new ProductService($mockRedis);
    }

    /** Test successful hold */
    public function test_hold_product_successfully(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->postJson('/api/holds', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'data']);

        $this->assertDatabaseHas('holds', [
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'quantity' => 2,
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 8
        ]);
    }

    /** Test hold fails when product does not exist */
    public function test_hold_product_not_found(): void
    {
        $response = $this->postJson('/api/holds', [
            'product_id' => 9999,
            'quantity' => 1
        ]);

        $response->assertStatus(404);

        // Ensure no holds were created
        $this->assertDatabaseCount('holds', 0);
    }

    /** Test hold fails when quantity exceeds stock */
    public function test_hold_product_quantity_not_enough(): void
    {
        $product = Product::factory()->create(['stock' => 3]);

        $response = $this->postJson('/api/holds', [
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Product quantity is not enough'
            ]);

        // Ensure stock was not deducted
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3
        ]);

        // Ensure no hold was created
        $this->assertDatabaseCount('holds', 0);
    }

    /** Test hold fails validation when quantity is missing or invalid */
    public function test_hold_validation_errors(): void
    {
        $product = Product::factory()->create(['stock' => 5]);

        // Missing quantity
        $response1 = $this->postJson('/api/holds', [
            'product_id' => $product->id,
        ]);
        $response1->assertStatus(422)
            ->assertJsonFragment(['message' => 'The quantity field is required.']);

        // Quantity less than 1
        $response2 = $this->postJson('/api/holds', [
            'product_id' => $product->id,
            'quantity' => 0
        ]);
        $response2->assertStatus(422)
            ->assertJsonFragment(['message' => 'The quantity field must be at least 1.']);

        // Ensure no holds were created
        $this->assertDatabaseCount('holds', 0);

        // Stock remains the same
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5
        ]);
    }

    /** Test hold fails if user is not authenticated */
    public function test_hold_unauthenticated(): void
    {
        $this->actingAsGuest();
        $product = Product::factory()->create(['stock' => 5]);

        $this->postJson('/api/holds', [
            'product_id' => $product->id,
            'quantity' => 1
        ])->assertStatus(401);

        // Ensure no holds were created
        $this->assertDatabaseCount('holds', 0);

        // Stock remains the same
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5
        ]);
    }
}
