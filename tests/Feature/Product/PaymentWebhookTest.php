<?php

namespace Tests\Feature\Payment;

use App\Models\Hold;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;
    private Hold $hold;
    private Order $order;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->product = Product::factory()->create(['stock' => 10]);

        $this->hold = Hold::factory()->create([
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'quantity' => 2,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->order = $this->user->orders()->create([
            'hold_id' => $this->hold->id,
            'amount' => 100,
            'status' => 'pending'
        ]);
    }

    public function test_payment_success(): void
    {
        $payload = [
            'order_id' => $this->order->id,
            'status' => 'success',
            'idempotency_key' => 'success-key-1'
        ];

        $response = $this->postJson('/api/payments/webhook', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.order_status', 'paid');

        $this->assertDatabaseHas('orders', [
            'id'     => $this->order->id,
            'status' => 'paid'
        ]);

        $this->assertDatabaseHas('payment_webhooks', [
            'order_id'        => $this->order->id,
            'idempotency_key' => 'success-key-1',
            'status'          => 'success'
        ]);

        $this->assertDatabaseHas('holds', [
            'id'     => $this->hold->id,
            'status' => 'pending'
        ]);
    }

    public function test_payment_failed_releases_hold(): void
    {
        $payload = [
            'order_id' => $this->order->id,
            'status' => 'failed',
            'idempotency_key' => 'fail-key-1'
        ];

        $response = $this->postJson('/api/payments/webhook', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.order_status', 'failed');

        $this->assertDatabaseHas('orders', [
            'id'     => $this->order->id,
            'status' => 'failed'
        ]);

        $this->assertDatabaseHas('payment_webhooks', [
            'order_id'        => $this->order->id,
            'idempotency_key' => 'fail-key-1',
            'status'          => 'failed'
        ]);

        $this->assertDatabaseHas('holds', [
            'id' => $this->hold->id,
            'status' => 'failed'
        ]);

        $this->assertEquals(12, $this->product->fresh()->stock);

        // try use hold again:
        $res = $this->postJson('/api/orders', [
            'hold_id' => $this->hold->id
        ]);
        $res->assertStatus(422);
    }

    public function test_payment_canceled_releases_hold(): void
    {
        $payload = [
            'order_id' => $this->order->id,
            'status' => 'canceled',
            'idempotency_key' => 'cancel-key-1'
        ];

        $response = $this->postJson('/api/payments/webhook', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.order_status', 'pending');

        $this->assertDatabaseHas('orders', [
            'id'     => $this->order->id,
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('payment_webhooks', [
            'order_id'        => $this->order->id,
            'idempotency_key' => 'cancel-key-1',
            'status'          => 'canceled'
        ]);

        $this->assertDatabaseHas('holds', [
            'id' => $this->hold->id
        ]);
    }

    public function test_idempotency_key_duplicate(): void
    {
        $payload = [
            'order_id' => $this->order->id,
            'status' => 'success',
            'idempotency_key' => 'duplicate-key'
        ];

        $this->postJson('/api/payments/webhook', $payload)->assertStatus(200);

        $response = $this->postJson('/api/payments/webhook', $payload);

        $response->assertStatus(422);

        $this->assertDatabaseCount('payment_webhooks', 1);
    }
}
