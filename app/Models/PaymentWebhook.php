<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWebhook extends Model
{
    protected $table = "payment_webhooks";

    protected $fillable = [
        'idempotency_key', // idempotency_key or whatever name do you want "transaction_id" ,"tracking_id", "idempotency_key"
        'refund_id',
        'order_id',
        'amount',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
