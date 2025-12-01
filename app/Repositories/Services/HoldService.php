<?php

namespace App\Repositories\Services;

use App\Domain\Interfaces\RedisInterface;
use App\Models\User;
use App\Repositories\Interfaces\HoldInterface;
use App\Repositories\Interfaces\ProductInterface;
use App\Traits\LoggerTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;

class HoldService implements HoldInterface
{
    use LoggerTrait;
    private RedisInterface $redis;
    private ProductInterface $productRepository;

    public function __construct(RedisInterface $redis, ProductInterface $productRepository)
    {
        $this->redis = $redis;
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        return [];
    }

    public function show(int $id)
    {
        return [];
    }

    public function store($product, $quantity)
    {
        $user = $this->getUser();
        $hold = null;
        DB::transaction(function () use ($product, $quantity, $user, &$hold) {
            $hold = $user->holds()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'expires_at' => now()->addMinutes(2),
                'status' => 'pending'
            ]);
            $this->productRepository->updateQuantity($product, $quantity);

            return $hold->load('product');
        }, 5);

        return $hold;
    }

    public function release($hold)
    {

        DB::beginTransaction();
        try {
            $hold->product()->update([
                'stock' => $hold->product->stock + $hold->quantity
            ]);
            // delete hold:
            $hold->delete();
            DB::commit();
        } catch (\Throwable $th) {
            $this->errorLogging($th);
            DB::rollBack();
        }
    }



    public function update($product, $quantity)
    {
        return [];
    }

    public function expired()
    {
        return [];
    }


    public function getUser(): User
    {
        /** @var \App\Models\User */
        $user = request()->user();
        if (!$user) {
            throw new AuthenticationException("Login first and try again latter");
        }

        return $user;
    }
}
