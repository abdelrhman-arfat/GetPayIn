<?php

namespace App\Repositories\Services;

use App\Domain\Interfaces\RedisInterface;
use App\Exceptions\Forbidden;
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

    public function show(int $id): object
    {
        $key = $this->cacheKey($id);
        $product = $this->redis->remember($key, 300, function () use ($id) {
            return Product::findOrFail($id);
        });
        return json_decode($product);
    }

    public function update(int|object $id, $data = null)
    {
        $product = $this->extractProduct($id);
        $product->update($data);
        $key = $this->cacheKey($product->id);
        $this->redis->set($key, json_encode($product));
        return $product;
    }

    public function hasQuantity(int|object $pr, int $quantity): bool
    {
        $product = $this->extractProduct($pr);

        $productQuantity = $product->stock;

        return $productQuantity >= $quantity;
    }

    public function updateQuantity(int|object $pr, int $quantity)
    {
        $product = $this->extractProduct($pr, false);
        if (!$this->hasQuantity($product, $quantity)) throw new Forbidden("Product quantity is not enough", 422);
        $product->stock = $product->stock - $quantity;
        $product->save();
        $key = $this->cacheKey($product->id);
        $this->redis->set($key, json_encode($product));
    }

    private function cacheKey(string $key): string
    {
        return $this->cachePrefix . ':' . $key;
    }


    private function extractProduct(int|object $pr, bool $cache = true)
    {
        if ($pr instanceof Product || !is_int($pr)) {
            if ($cache) return $pr;
            return Product::findOrFail($pr->id);
        }
        if ($cache) return $this->show($pr);
        return Product::findOrFail($pr);
    }
}
