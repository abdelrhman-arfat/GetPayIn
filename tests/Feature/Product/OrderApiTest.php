<?php

namespace Tests\Feature\Product;

use App\Domain\Services\FakeRedisService;
use App\Models\Hold;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;

    public function setUp(): void
    {
        parent::setUp();

        Product::factory()->count(5)->create();

        app()->bind(\App\Domain\Interfaces\RedisInterface::class, FakeRedisService::class);
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create(['stock' => 10]);
    }

    /** Test order is created successfully */
    public function test_create_order_successfully(): void
    {
        $hold = Hold::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'quantity' => 2,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(2)
        ]);

        $this->actingAs($this->user)
            ->postJson(route('api.order.store'), ['hold_id' => $hold->id])
            ->assertStatus(201)
            ->assertJsonFragment(['status' => true]);

        $this->assertDatabaseHas('orders', [
            'hold_id' => $hold->id,
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('holds', [
            'id' => $hold->id,
            'status' => 'success'
        ]);
    }

    /** Test order fails if hold is expired */
    public function test_order_fails_if_hold_expired(): void
    {
        $hold = Hold::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'quantity' => 1,
            'status' => 'pending',
            'expires_at' => now()->subMinutes(1)
        ]);

        $this->actingAs($this->user)
            ->postJson(route('api.order.store'), ['hold_id' => $hold->id])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Hold is not available']);

        $this->assertDatabaseMissing('holds', [
            'id' => $hold->id,
        ]);

        $this->assertEquals(11, $this->product->fresh()->stock);
    }

    public function test_duplicated_hold(): void
    {
        $hold = Hold::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'quantity' => 1,
            'status' => 'success',
            'used_at' => now()->subMinutes(1),
            'expires_at' => now()->addMinutes(1)
        ]);
        $this->actingAs($this->user)
            ->postJson(route('api.order.store'), ['hold_id' => $hold->id])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Hold is not available']);
    }

    /** Test order fails if hold is already used */
    public function test_order_fails_if_hold_already_used(): void
    {
        $hold = Hold::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'quantity' => 1,
            'status' => 'success',
            'used_at' => now()
        ]);

        $this->actingAs($this->user)
            ->postJson(route('api.order.store'), ['hold_id' => $hold->id])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Hold is not available']);
    }

    /** Test order fails if hold does not exist */
    public function test_order_fails_if_hold_not_found(): void
    {
        $this->actingAs($this->user)
            ->postJson(route('api.order.store'), ['hold_id' => 9999])
            ->assertStatus(422)
            ->assertJsonFragment(['message' => 'Hold is not available']);
    }

    /** Test order fails if user is not authenticated */
    public function test_order_fails_if_unauthenticated(): void
    {
        $hold = Hold::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'quantity' => 1,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(2)
        ]);

        $this->postJson(route('api.order.store'), ['hold_id' => $hold->id])
            ->assertStatus(401);
    }
}
