<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hold extends Model
{
    use HasFactory;
    protected $table = "holds";

    protected $fillable = [
        "product_id",
        'user_id',
        "quantity",
        "status",
        "expires_at"
    ];


    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        // Set expires_at automatically when creating a new hold
        static::creating(function ($hold) {
            // Only set expires_at if it's not already provided
            if (empty($hold->expires_at)) {
                $hold->expires_at = Carbon::now()->addMinutes(2);
            }
        });
    }


    //  ============================ Scopes ============================ 
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeExpired($query)
    {
        return $query->where("status", "pending")
            ->whereNull("used_at")
            ->where('expires_at', '<=', now());
    }

    public function scopeUsed($query)
    {
        return $query->whereNotNull("used_at");
    }

    public function scopeAvailable($query)
    {
        return $query->where("status", "pending")
            ->whereNull("used_at")
            ->where('expires_at', '>', now());
    }



    public function isAvailable(): bool
    {
        return $this->status === 'pending' && $this->used_at === null && $this->expires_at > now();
    }
    //  ============================ Relations ============================ 
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    public function orders()
    {
        return $this->hasOne(Order::class, 'hold_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
