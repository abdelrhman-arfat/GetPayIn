<?php

namespace App\Repositories\Services;

use App\Exceptions\Forbidden;
use App\Models\Order;
use App\Repositories\Interfaces\OrderInterface;
use Illuminate\Support\Facades\DB;

class OrderService implements OrderInterface
{
    public function store($hold)
    {
        DB::transaction(function () use ($hold) {

            if (!$hold->isAvailable()) {
                throw new Forbidden("The hold is expired", 400);
            }

            $hold->update([
                'status' => 'success',
                'used_at' => now()
            ]);

            return Order::create([
                'hold_id' => $hold->id,
                'status' => 'pending',
                'amount' => $hold->product->price * $hold->quantity
            ]);
        }, 3);
        return null;
    }

    public function show($id)
    {
        return Order::with('hold.product')->find($id);
    }
}
