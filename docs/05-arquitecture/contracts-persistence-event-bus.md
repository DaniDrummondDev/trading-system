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

## Contratos de Embeddings & RAG

### EmbeddingService

```php
namespace App\Application\Contracts;

use App\Domain\Shared\ValueObjects\EmbeddingVector;

interface EmbeddingService
{
    /**
     * Converte texto em vetor de embedding.
     */
    public function generateEmbedding(string $text): EmbeddingVector;

    /**
     * Gera embeddings em lote para múltiplos textos.
     *
     * @param  string[]  $texts
     * @return EmbeddingVector[]
     */
    public function generateBatchEmbeddings(array $texts): array;
}
```

---

### AiAnalysisRepository

```php
namespace App\Application\Contracts;

use App\Domain\Shared\ValueObjects\EmbeddingVector;

interface AiAnalysisRepository
{
    /**
     * Persiste uma análise com seu embedding.
     */
    public function save(AiAnalysis $analysis): void;

    /**
     * Busca as N análises mais similares via cosine distance (pgvector).
     *
     * @return AiAnalysis[]
     */
    public function findSimilar(
        EmbeddingVector $query,
        int $limit = 5,
        ?string $analysisType = null,
    ): array;

    /**
     * Recupera todas as análises associadas a um trade.
     *
     * @return AiAnalysis[]
     */
    public function findByTradeId(string $tradeId): array;
}
```

---

### EmbeddingVector (Value Object — Domain)

```php
namespace App\Domain\Shared\ValueObjects;

class EmbeddingVector
{
    /**
     * @param  float[]  $dimensions  Vetor de dimensões (ex: 1536 para OpenAI small)
     */
    public function __construct(
        public readonly array $dimensions,
    ) {
        if (empty($dimensions)) {
            throw new \InvalidArgumentException('Embedding vector cannot be empty.');
        }
    }

    public function dimensionCount(): int
    {
        return count($this->dimensions);
    }

    /**
     * Formato compatível com pgvector: '[0.1, 0.2, ...]'
     */
    public function toPgVector(): string
    {
        return '[' . implode(',', $this->dimensions) . ']';
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

### Embedding & RAG Implementations

```
App\Infrastructure\AI\EmbeddingService\OpenAIEmbeddingService
```

Implementa `EmbeddingService` usando OpenAI text-embedding-3-small (1536 dimensões).

```
App\Infrastructure\AI\EmbeddingService\OllamaEmbeddingService
```

Alternativa local via Ollama nomic-embed-text (768 dimensões). Sem dependência externa.

```
App\Infrastructure\Persistence\Repositories\AiAnalysisRepositoryPgVector
```

Implementa `AiAnalysisRepository` usando pgvector (operador `<=>` cosine distance + índice HNSW).

```
App\Infrastructure\AI\RagContextBuilder
```

Busca análises similares via `AiAnalysisRepository` e formata como contexto
para injeção nos prompts da IA.

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

    // Embeddings & RAG
    $this->app->bind(
        EmbeddingService::class,
        OpenAIEmbeddingService::class
    );

    $this->app->bind(
        AiAnalysisRepository::class,
        AiAnalysisRepositoryPgVector::class
    );
}
```

---

## Observações Críticas

* Domain nunca conhece Event Bus
* Application só publica eventos
* IA consome eventos, não chama casos de uso
* Pronto para escalar para async
* Embeddings são gerados **assincronamente** (não bloqueiam fluxo principal)
* RAG é **desligável** via feature flag independente da IA
* `EmbeddingVector` é o único artefato de embedding no Domain (Value Object puro)
* Se o serviço de embedding falhar, o sistema continua operando sem RAG
