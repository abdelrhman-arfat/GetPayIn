<?php

namespace Tests\Feature\Product;

use App\Domain\Services\FakeRedisService;
use App\Models\Hold;
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

        app()->bind(\App\Domain\Interfaces\RedisInterface::class, \App\Domain\Services\FakeRedisService::class);
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** Test successful hold */
    public function test_hold_product_successfully(): void
    {
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->postJson('/api/holds', [
            'product_id' => $product->id,
            'qty' => 2
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['status', 'data' => [
                'hold_id',
                'expires_at'
            ]]);

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
            'qty' => 1
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
            'qty' => 5
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
            'qty' => 0
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
            'qty' => 1
        ])->assertStatus(401);

        // Ensure no holds were created
        $this->assertDatabaseCount('holds', 0);

        // Stock remains the same
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5
        ]);
    }



    public function test_parallel_hold_attempts_multiple_allowed_but_no_oversell(): void
    {
        // Product starts with stock = 3
        $product = Product::factory()->create(['stock' => 3]);

        // Create 5 users simulating parallel buyers
        $users = User::factory()->count(5)->create();

        $results = [];

        foreach ($users as $user) {
            try {
                // Each user tries to place a hold of 1
                $response = $this->actingAs($user)->postJson('/api/holds', [
                    'product_id' => $product->id,
                    'qty' => 1
                ]);

                $results[] = $response->status() === 201 ? 'success' : 'failed';
            } catch (\Exception $e) {
                $results[] = 'failed';
            }
        }

        $successCount = collect($results)->filter(fn($r) => $r === 'success')->count();

        // Only up to stock should succeed
        $this->assertEquals(3, $successCount, "Only 3 holds should succeed — matching stock.");
        $this->assertDatabaseCount('holds', 3);

        $this->assertEquals(0, $product->fresh()->stock);
    }


    public function test_command_clean_expired_holds()
    {
        $P = Product::factory()->create(['stock' => 3]);
        Hold::factory()->create([
            'expires_at' => now()->subMinutes(3),
            'product_id' => $P->id,
            'user_id' => $this->user->id,
            'quantity' => 1,
            'status' => 'pending'
        ]);

        $this->artisan('holds:cleanup')->assertExitCode(0);

        $this->assertDatabaseCount('holds', 0);
        $this->assertEquals(4, $P->fresh()->stock);
    }

    public function test_hold_rule_restores_stock_and_deletes_invalid_hold()
    {
        $product = Product::factory()->create(['stock' => 5]);

        // Create an expired hold
        $hold = Hold::factory()->create([
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'quantity' => 2,
            'expires_at' => now()->subMinutes(5),
            'status' => 'pending'
        ]);

        $response = $this->postJson('/api/orders', [
            'hold_id' => $hold->id
        ]);

        // Should fail validation
        $response->assertStatus(422)
            ->assertJsonFragment(['Hold is not available']);

        // Hold must be deleted
        $this->assertDatabaseMissing('holds', [
            'id' => $hold->id,
        ]);

        $this->assertEquals(7, $product->fresh()->stock);
    }

    public function test_hold_rule_fails_if_hold_does_not_exist()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->postJson('/api/orders', [
            'hold_id' => 999999
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Hold is not available']);

        $this->assertEquals(10, $product->fresh()->stock);
    }

    public function test_hold_rule_fails_if_hold_was_already_used()
    {
        $product = Product::factory()->create(['stock' => 5]);

        $hold = Hold::factory()->create([
            'product_id' => $product->id,
            'user_id' => $this->user->id,
            'quantity' => 1,
            'used_at' => now()->subMinute(),
            'status' => 'pending'
        ]);

        $response = $this->postJson('/api/orders', [
            'hold_id' => $hold->id
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['Hold is not available']);

        $this->assertDatabaseMissing('holds', ['id' => $hold->id]);

        $this->assertEquals(6, $product->fresh()->stock);
    }

    public function test_command_clean_expired_holds_no_updates()
    {
        $P = Product::factory()->create(['stock' => 3]);
        Hold::factory()->create([
            'expires_at' => now()->subMinutes(3),
            'product_id' => $P->id,
            'user_id' => $this->user->id,
            'quantity' => 1,
            'used_at' => now()->subMinutes(1),
            'status' => 'pending'
        ]);
        Hold::factory()->create([
            'expires_at' => now()->subMinutes(3),
            'product_id' => $P->id,
            'user_id' => $this->user->id,
            'quantity' => 1,
            'status' => 'success'
        ]);

        $this->artisan('holds:cleanup')->assertExitCode(0);

        $this->assertDatabaseCount('holds', 2);
        $this->assertEquals(3, $P->fresh()->stock);
    }
}
