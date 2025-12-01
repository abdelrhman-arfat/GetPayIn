<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'hold_id',
        'status',
        'amount'
    ];

    public function product()
    {
        return $this->hasOneThrough(
            Product::class,
            Hold::class,
            'id',
            'id',
            'hold_id',
            'product_id'
        );
    }

    public function hold()
    {
        return $this->belongsTo(Hold::class, 'hold_id', 'id');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Hold::class,
            'id',
            'id',
            'hold_id',
            'user_id'
        );
    }

    public function payment()
    {
        return $this->hasOne(PaymentWebhook::class, 'order_id', "id");
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeCanceled($query)
    {
        return $query->where('status', 'canceled');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function isAvailable(): bool
    {
        return $this->status === 'pending';
    }
}
