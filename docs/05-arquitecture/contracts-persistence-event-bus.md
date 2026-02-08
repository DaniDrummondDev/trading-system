# Contratos de Persistência + Event Bus

## Objetivo

Definir contratos estáveis entre Application e Infrastructure para:

* Persistência de dados
* Publicação de eventos de domínio
* Integração futura com IA, mensageria e SaaS

---

## Princípios

* Application depende apenas de interfaces
* Infrastructure implementa contratos
* Eventos são imutáveis
* Event Bus desacoplado (sync hoje, async amanhã)

---

## Contratos de Persistência (Repositories)

### TradeRepository

```php
namespace App\Application\Contracts;

use App\Domain\Trade\Aggregates\TradeAggregate;

interface TradeRepository
{
    public function save(TradeAggregate $trade): void;

    public function getById(string $tradeId): TradeAggregate;

    public function getOpenTrades(): array;
}
```

---

### TradeJournalRepository

```php
namespace App\Application\Contracts;

use App\Domain\Journal\Entities\TradeJournal;

interface TradeJournalRepository
{
    public function save(TradeJournal $journal): void;

    public function getByTradeId(string $tradeId): TradeJournal;
}
```

---

### MetricsRepository

```php
namespace App\Application\Contracts;

use App\Domain\Metrics\Entities\TraderMetrics;

interface MetricsRepository
{
    public function update(TraderMetrics $metrics): void;

    public function getCurrent(): TraderMetrics;
}
```

---

## Contratos de Event Bus

### DomainEvent

```php
namespace App\Domain\Shared\Events;

interface DomainEvent
{
    public function occurredOn(): \DateTimeImmutable;
}
```

---

### EventPublisher

```php
namespace App\Application\Contracts;

use App\Domain\Shared\Events\DomainEvent;

interface EventPublisher
{
    public function publish(DomainEvent $event): void;
}
```

---

## Eventos de Domínio

### TradeClosed

```php
namespace App\Domain\Trade\Events;

use App\Domain\Shared\Events\DomainEvent;

class TradeClosed implements DomainEvent
{
    public function __construct(
        public readonly string $tradeId,
        public readonly float $result
    ) {}

    public function occurredOn(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
```

---

### LearningDataAvailable

```php
namespace App\Domain\Metrics\Events;

use App\Domain\Shared\Events\DomainEvent;

class LearningDataAvailable implements DomainEvent
{
    public function __construct(
        public readonly string $tradeId
    ) {}

    public function occurredOn(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
```

---

## Implementações (Infrastructure)

### Repository Implementation

```
App\Infrastructure\Persistence\Repositories\TradeRepositoryEloquent
```

Implementa `TradeRepository` usando Eloquent.

---

### Event Bus Implementation (Laravel)

```
App\Infrastructure\EventBus\LaravelEventPublisher
```

* Usa `event()` internamente
* Pode ser trocado por RabbitMQ / Kafka no futuro

---

## Bindings (Service Providers)

### InfrastructureServiceProvider

```php
public function register(): void
{
    $this->app->bind(
        TradeRepository::class,
        TradeRepositoryEloquent::class
    );

    $this->app->bind(
        EventPublisher::class,
        LaravelEventPublisher::class
    );
}
```

---

## Observações Críticas

* Domain nunca conhece Event Bus
* Application só publica eventos
* IA consome eventos, não chama casos de uso
* Pronto para escalar para async
