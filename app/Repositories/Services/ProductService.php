<?php

namespace App\Repositories\Services;

use App\Domain\Interfaces\RedisInterface;
use App\Models\Product;
use App\Repositories\Interfaces\ProductInterface;

class ProductService implements ProductInterface
{

    private RedisInterface $redis;
    private string $cachePrefix = 'products';


    public function __construct(RedisInterface $redis)
    {
        $this->redis = $redis;
    }

    public function index()
    {
        return [];
    }

    public function show(int $id)
    {
        $key = $this->cacheKey($id);
        $product = $this->redis->remember($key, 300, function () use ($id) {
            return Product::findOrFail($id);
        });
        return json_decode($product);
    }

    public function hasQuantity(int $id, int $quantity)
    {
        /** @var Product */
        $product = $this->show($id);
        $productQuantity = $product->stock;

        return $productQuantity >= $quantity;
    }


    private function cacheKey(string $key): string
    {
        return $this->cachePrefix . ':' . $key;
    }
}
