<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Database:
        $this->app->bind(\App\Repositories\Interfaces\ProductInterface::class, \App\Repositories\Services\ProductService::class);
        $this->app->bind(\App\Repositories\Interfaces\HoldInterface::class, \App\Repositories\Services\HoldService::class);
        $this->app->bind(\App\Repositories\Interfaces\OrderInterface::class, \App\Repositories\Services\OrderService::class);
        $this->app->bind(\App\Repositories\Interfaces\UserInterface::class, \App\Repositories\Services\UserService::class);
        $this->app->bind(\App\Repositories\Interfaces\PaymentWebhookInterface::class, \App\Repositories\Services\PaymentWebhookService::class);
        // Domains:
        $this->app->bind(\App\Domain\Interfaces\RedisInterface::class, \App\Domain\Services\RedisService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
