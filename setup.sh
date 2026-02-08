#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info()  { echo -e "${BLUE}[INFO]${NC} $1"; }
log_ok()    { echo -e "${GREEN}[OK]${NC} $1"; }
log_warn()  { echo -e "${YELLOW}[WARN]${NC} $1"; }
log_error() { echo -e "${RED}[ERROR]${NC} $1"; }

echo ""
echo "========================================"
echo "  Trading System - Project Setup"
echo "  PHP 8.5 | Laravel 12 | PostgreSQL 18"
echo "========================================"
echo ""

# --------------------------------------------------
# Pre-flight checks
# --------------------------------------------------
log_info "Checking prerequisites..."

if ! command -v docker &> /dev/null; then
    log_error "Docker is not installed. Please install Docker first."
    exit 1
fi

if ! docker compose version &> /dev/null; then
    log_error "Docker Compose V2 is not available. Please update Docker."
    exit 1
fi

log_ok "Docker and Docker Compose found."

# --------------------------------------------------
# Step 1: Build Docker images
# --------------------------------------------------
log_info "[1/8] Building Docker images..."
docker compose build --no-cache
log_ok "Docker images built."

# --------------------------------------------------
# Step 2: Install Laravel 12
# --------------------------------------------------
if [ -f "artisan" ]; then
    log_warn "Laravel already installed. Skipping installation."
else
    log_info "[2/8] Installing Laravel 12..."
    docker compose run --rm --no-deps app bash -c '
        composer create-project laravel/laravel /tmp/laravel "^12.0" --prefer-dist --no-interaction

        # Copy Laravel files to project root, preserving existing files
        shopt -s dotglob
        for item in /tmp/laravel/*; do
            basename=$(basename "$item")
            # Skip directories we want to preserve
            if [ "$basename" != ".git" ] && [ "$basename" != ".claude" ] && [ "$basename" != "docs" ] && [ "$basename" != "docker" ]; then
                cp -r "$item" /var/www/
            fi
        done

        rm -rf /tmp/laravel
    '
    # Fix file ownership (Docker runs as root)
    docker compose run --rm --no-deps app chown -R $(id -u):$(id -g) /var/www
    log_ok "Laravel 12 installed."
fi

# --------------------------------------------------
# Step 3: Configure .env for Docker
# --------------------------------------------------
log_info "[3/8] Configuring environment..."
if [ -f ".env" ]; then
    # Update existing .env with Docker-specific values
    sed -i 's|^APP_NAME=.*|APP_NAME="Trading System"|' .env
    sed -i 's|^APP_URL=.*|APP_URL=http://localhost:8000|' .env
    sed -i 's|^DB_CONNECTION=.*|DB_CONNECTION=pgsql|' .env
    sed -i 's|^DB_HOST=.*|DB_HOST=postgres|' .env
    sed -i 's|^DB_PORT=.*|DB_PORT=5432|' .env
    sed -i 's|^DB_DATABASE=.*|DB_DATABASE=trading_system|' .env
    sed -i 's|^DB_USERNAME=.*|DB_USERNAME=trading|' .env
    sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD=trading_secret|' .env
    sed -i 's|^CACHE_STORE=.*|CACHE_STORE=redis|' .env
    sed -i 's|^SESSION_DRIVER=.*|SESSION_DRIVER=redis|' .env
    sed -i 's|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=sync|' .env
    sed -i 's|^REDIS_HOST=.*|REDIS_HOST=redis|' .env
else
    cp .env.example .env
    sed -i 's|^APP_NAME=.*|APP_NAME="Trading System"|' .env
    sed -i 's|^APP_URL=.*|APP_URL=http://localhost:8000|' .env
    sed -i 's|^DB_CONNECTION=.*|DB_CONNECTION=pgsql|' .env
    sed -i 's|^DB_HOST=.*|DB_HOST=postgres|' .env
    sed -i 's|^DB_PORT=.*|DB_PORT=5432|' .env
    sed -i 's|^DB_DATABASE=.*|DB_DATABASE=trading_system|' .env
    sed -i 's|^DB_USERNAME=.*|DB_USERNAME=trading|' .env
    sed -i 's|^DB_PASSWORD=.*|DB_PASSWORD=trading_secret|' .env
    sed -i 's|^CACHE_STORE=.*|CACHE_STORE=redis|' .env
    sed -i 's|^SESSION_DRIVER=.*|SESSION_DRIVER=redis|' .env
    sed -i 's|^QUEUE_CONNECTION=.*|QUEUE_CONNECTION=sync|' .env
    sed -i 's|^REDIS_HOST=.*|REDIS_HOST=redis|' .env
fi
log_ok "Environment configured."

# --------------------------------------------------
# Step 4: Install additional packages
# --------------------------------------------------
log_info "[4/8] Installing packages (Laravel AI SDK, Pest 4)..."
docker compose run --rm --no-deps app composer require laravel/ai --no-interaction
docker compose run --rm --no-deps app composer require pestphp/pest:^4.0 phpunit/phpunit:^12.5 pestphp/pest-plugin-arch --dev --with-all-dependencies --no-interaction
log_ok "Packages installed."

# --------------------------------------------------
# Step 5: Configure Laravel for API-only
# --------------------------------------------------
log_info "[5/8] Configuring Laravel API..."
docker compose run --rm --no-deps app php artisan install:api --no-interaction || true
docker compose run --rm --no-deps app ./vendor/bin/pest --init
# Fix ownership after package installations
docker compose run --rm --no-deps app chown -R $(id -u):$(id -g) /var/www
log_ok "API and Pest configured."

# --------------------------------------------------
# Step 6: Create Clean Architecture folder structure
# --------------------------------------------------
log_info "[6/8] Creating Clean Architecture folder structure..."

# Domain Layer
DOMAIN_DIRS=(
    "app/Domain/Trade/Entities"
    "app/Domain/Trade/ValueObjects"
    "app/Domain/Trade/Aggregates"
    "app/Domain/Trade/Events"
    "app/Domain/Journal/Entities"
    "app/Domain/Journal/ValueObjects"
    "app/Domain/Journal/Events"
    "app/Domain/Metrics/Entities"
    "app/Domain/Metrics/ValueObjects"
    "app/Domain/Shared/Events"
)

# Application Layer
APP_DIRS=(
    "app/Application/TradeExecution/Commands"
    "app/Application/TradeExecution/Queries"
    "app/Application/TradeExecution/Handlers"
    "app/Application/TradeExecution/DTOs"
    "app/Application/TradeJournal/Commands"
    "app/Application/TradeJournal/Queries"
    "app/Application/TradeJournal/Handlers"
    "app/Application/TradeJournal/DTOs"
    "app/Application/Contracts"
)

# Infrastructure Layer
INFRA_DIRS=(
    "app/Infrastructure/Persistence/Eloquent"
    "app/Infrastructure/Persistence/Repositories"
    "app/Infrastructure/MarketData"
    "app/Infrastructure/AI/PromptBuilders"
    "app/Infrastructure/AI/FeatureExtractors"
    "app/Infrastructure/AI/LearningPipeline"
    "app/Infrastructure/EventBus"
)

# Interfaces Layer
INTERFACE_DIRS=(
    "app/Interfaces/Http/Controllers"
    "app/Interfaces/Http/Requests"
    "app/Interfaces/Console/Commands"
)

# Test Structure
TEST_DIRS=(
    "tests/Unit/Domain/Trade"
    "tests/Unit/Domain/Journal"
    "tests/Unit/Domain/Metrics"
    "tests/Unit/Application/TradeExecution"
    "tests/Unit/Application/TradeJournal"
    "tests/Integration/Infrastructure/Persistence"
    "tests/Integration/Infrastructure/MarketData"
    "tests/Integration/Infrastructure/AI"
    "tests/Feature/Interfaces/Http"
)

ALL_DIRS=("${DOMAIN_DIRS[@]}" "${APP_DIRS[@]}" "${INFRA_DIRS[@]}" "${INTERFACE_DIRS[@]}" "${TEST_DIRS[@]}")

for dir in "${ALL_DIRS[@]}"; do
    mkdir -p "$dir"
    if [ ! -f "$dir/.gitkeep" ]; then
        touch "$dir/.gitkeep"
    fi
done

log_ok "Folder structure created."

# --------------------------------------------------
# Step 7: Create base architectural files
# --------------------------------------------------
log_info "[7/8] Creating base architectural files..."

# --- Domain: DomainEvent Interface ---
cat > app/Domain/Shared/Events/DomainEvent.php << 'PHP'
<?php

declare(strict_types=1);

namespace App\Domain\Shared\Events;

interface DomainEvent
{
    public function occurredOn(): \DateTimeImmutable;
}
PHP

# --- Application: Contracts ---
cat > app/Application/Contracts/TradeRepository.php << 'PHP'
<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Trade\Aggregates\TradeAggregate;

interface TradeRepository
{
    public function save(TradeAggregate $trade): void;

    public function getById(string $tradeId): TradeAggregate;

    public function getOpenTrades(): array;
}
PHP

cat > app/Application/Contracts/TradeJournalRepository.php << 'PHP'
<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Journal\Entities\TradeJournal;

interface TradeJournalRepository
{
    public function save(TradeJournal $journal): void;

    public function getByTradeId(string $tradeId): TradeJournal;
}
PHP

cat > app/Application/Contracts/MetricsRepository.php << 'PHP'
<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Metrics\Entities\TraderMetrics;

interface MetricsRepository
{
    public function update(TraderMetrics $metrics): void;

    public function getCurrent(): TraderMetrics;
}
PHP

cat > app/Application/Contracts/EventPublisher.php << 'PHP'
<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Shared\Events\DomainEvent;

interface EventPublisher
{
    public function publish(DomainEvent $event): void;
}
PHP

# --- Service Providers ---
cat > app/Providers/DomainServiceProvider.php << 'PHP'
<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Domain event listeners and registrations
    }

    public function boot(): void
    {
        //
    }
}
PHP

cat > app/Providers/InfrastructureServiceProvider.php << 'PHP'
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
PHP

# Register providers in bootstrap/providers.php
if [ -f "bootstrap/providers.php" ]; then
    # Add our custom providers if not already registered
    if ! grep -q "DomainServiceProvider" bootstrap/providers.php; then
        sed -i 's|return \[|return [\n    App\\Providers\\DomainServiceProvider::class,\n    App\\Providers\\InfrastructureServiceProvider::class,|' bootstrap/providers.php
    fi
fi

# Remove .gitkeep from directories that now have files
rm -f app/Domain/Shared/Events/.gitkeep
rm -f app/Application/Contracts/.gitkeep

log_ok "Base architectural files created."

# --------------------------------------------------
# Step 8: Start services and finalize
# --------------------------------------------------
log_info "[8/8] Starting services..."
docker compose up -d

# Wait for services to be healthy
log_info "Waiting for services to be ready..."
sleep 10

# Generate application key
docker compose exec app php artisan key:generate --no-interaction

# Set permissions
docker compose exec app chmod -R 775 storage bootstrap/cache

# Run migrations
docker compose exec app php artisan migrate --no-interaction

# Clear and cache config
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear

log_ok "Services started and configured."

# --------------------------------------------------
# Done
# --------------------------------------------------
echo ""
echo "========================================"
echo -e "  ${GREEN}Trading System - Setup Complete!${NC}"
echo "========================================"
echo ""
echo "  Services:"
echo "    API:        http://localhost:8000"
echo "    PostgreSQL:  localhost:5432"
echo "    Redis:       localhost:6379"
echo ""
echo "  Commands:"
echo "    docker compose up -d       Start services"
echo "    docker compose down        Stop services"
echo "    docker compose exec app    Enter app container"
echo ""
echo "  Testing:"
echo "    docker compose exec app ./vendor/bin/pest"
echo ""
echo "  Architecture:"
echo "    app/Domain/          Pure business rules"
echo "    app/Application/     Use cases & orchestration"
echo "    app/Infrastructure/  Technical implementations"
echo "    app/Interfaces/      HTTP Controllers & CLI"
echo ""
