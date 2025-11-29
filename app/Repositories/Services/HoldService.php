<?php

namespace App\Repositories\Services;

use App\Domain\Interfaces\RedisInterface;
use App\Models\User;
use App\Repositories\Interfaces\HoldInterface;
use App\Repositories\Interfaces\ProductInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;

class HoldService implements HoldInterface
{
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
                'status' => 'pending'
            ]);
            $this->productRepository->updateQuantity($product, $quantity);
        }, 3);

        return $hold;
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
        $user = auth()->user();
        if (!$user) {
            throw new AuthenticationException("Login first and try again latter");
        }

        return $user;
    }
}
