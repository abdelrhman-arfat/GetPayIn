<?php

namespace App\Repositories\Services;

use App\Domain\Interfaces\RedisInterface;
use App\Exceptions\Forbidden;
use App\Models\Product;
use App\Repositories\Interfaces\ProductInterface;
use Illuminate\Support\Facades\DB;

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
        return [];
    }

    public function hasQuantity(int|object $pr, int $quantity): bool
    {
        $product = $this->extractProduct($pr);

        $productQuantity = $product->stock;

        return $productQuantity >= $quantity;
    }

    public function updateQuantity(int|object $pr, int $quantity)
    {
        $id = $this->extractProduct($pr)->id;
        DB::transaction(function () use ($id, $quantity) {
            $product = DB::table('products')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();


            if ($product->stock < $quantity) {
                throw new Forbidden("Product quantity is not enough", 422);
            }

            DB::table('products')
                ->where('id', $id)
                ->update([
                    'stock' => $product->stock - $quantity
                ]);
        }, 5);
        $key = $this->cacheKey($id);
        $this->redis->delete($key);
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
