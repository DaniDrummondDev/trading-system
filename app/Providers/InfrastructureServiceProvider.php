<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class InfrastructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind Application contracts to Infrastructure implementations
        // $this->app->bind(
        //     \App\Application\Contracts\TradeRepository::class,
        //     \App\Infrastructure\Persistence\Repositories\TradeRepositoryEloquent::class
        // );
        // $this->app->bind(
        //     \App\Application\Contracts\EventPublisher::class,
        //     \App\Infrastructure\EventBus\LaravelEventPublisher::class
        // );
    }

    public function boot(): void
    {
        //
    }
}
