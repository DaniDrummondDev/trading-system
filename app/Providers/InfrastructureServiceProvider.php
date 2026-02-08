<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Contracts\EventPublisher;
use App\Application\Contracts\MetricsRepository;
use App\Application\Contracts\TradeJournalRepository;
use App\Application\Contracts\TradeRepository;
use App\Application\Contracts\UuidGenerator;
use App\Infrastructure\EventBus\LaravelEventPublisher;
use App\Infrastructure\Persistence\Repositories\MetricsRepositoryEloquent;
use App\Infrastructure\Persistence\Repositories\TradeJournalRepositoryEloquent;
use App\Infrastructure\Persistence\Repositories\TradeRepositoryEloquent;
use App\Infrastructure\Services\LaravelUuidGenerator;
use Illuminate\Support\ServiceProvider;

class InfrastructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TradeRepository::class, TradeRepositoryEloquent::class);
        $this->app->bind(TradeJournalRepository::class, TradeJournalRepositoryEloquent::class);
        $this->app->bind(MetricsRepository::class, MetricsRepositoryEloquent::class);
        $this->app->bind(EventPublisher::class, LaravelEventPublisher::class);
        $this->app->bind(UuidGenerator::class, LaravelUuidGenerator::class);
    }

    public function boot(): void
    {
        //
    }
}
