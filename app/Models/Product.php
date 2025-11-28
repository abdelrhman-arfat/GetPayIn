<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'price',
        'stock',
    ];

    public function holds()
    {
        return $this->hasMany(Hold::class, 'product_id', 'id');
    }

    public function orders()
    {
        return $this->hasManyThrow(
            Order::class,
            Hold::class,
            'product_id',
            'hold_id',
            'id',
            'id'
        );
    }
}
