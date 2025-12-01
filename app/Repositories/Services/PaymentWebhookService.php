<?php

namespace App\Repositories\Services;

use App\Exceptions\DuplicateEntry;
use App\Models\PaymentWebhook;
use App\Repositories\Interfaces\HoldInterface;
use App\Repositories\Interfaces\OrderInterface;
use App\Repositories\Interfaces\PaymentWebhookInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentWebhookService implements PaymentWebhookInterface
{

    private HoldInterface $holdRepository;
    private OrderInterface $orderRepository;

    public function __construct(HoldInterface $holdRepository)
    {
        $this->holdRepository = $holdRepository;
    }

    public function store($data, $order)
    {
        $idempotency_key = $data['idempotency_key'];

        if (PaymentWebhook::where('idempotency_key', $idempotency_key)->exists()) {
            throw new DuplicateEntry("Payment has already been processed with this idempotency key.", 422);
        }

        $status = $this->extractPaymentData($data["status"]);

        return DB::transaction(function () use ($data, $status, $order) {
            $holdReleased = false;

            $payment = PaymentWebhook::create([
                'idempotency_key' => $data['idempotency_key'],
                'order_id'        => $order->id,
                'status'          => $status,
                'amount'          => $order->amount
            ]);

            if ($status === "failed") {
                $hold = $order->hold;
                if ($hold) {
                    $order->update(['status' => "failed"]);
                    $hold->product()->update([
                        'stock' => $hold->product->stock + $hold->quantity
                    ]);
                    $hold->update(['status' => "failed"]);
                    $holdReleased = true;
                }
                return [
                    "order_id" => $order->id,
                    "order_status" => "failed",
                    "payment_status" => "failed",
                    "payment_id" => $payment->id,
                    "hold_released" => $holdReleased,
                ];
            }

            if ($status === "canceled") {
                $holdReleased = true;
                return [
                    "order_id" => $order->id,
                    "order_status" => "pending",
                    "payment_status" => $payment->status,
                    "payment_id" => $payment->id,
                    "hold_released" => false,
                ];
            }

            // SUCCESS → update order to paid
            if ($status === "success") {
                $order->update(['status' => 'paid']);
            }

            return [
                "order_id" => $order->id,
                "order_status" => $order->status,
                "payment_status" => $payment->status,
                "payment_id" => $payment->id,
                "hold_released" => $holdReleased,
            ];
        });
    }










    private function extractPaymentData(string $status): string
    {
        $statusMap = [
            'success'       => 'success',
            'paid'          => 'success',

            'failed'        => 'failed',
            'failure'       => 'failed',
            'failing'       => 'failed',

            'canceled'      => 'canceled',
            'cancels'       => 'canceled',
            'cancelling'    => 'canceled',
            'cancelled'     => 'canceled',
        ];

        $status = strtolower($status);

        // إذا status غير معروف، اجعلها failed
        return $statusMap[$status] ?? 'failed';
    }
}
