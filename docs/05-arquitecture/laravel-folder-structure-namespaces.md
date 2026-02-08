# Estrutura Inicial de Pastas + Namespaces (Laravel-ready)

## Princípios

* Laravel como framework de entrega (não dono do domínio)
* Clean Architecture respeitada
* Namespaces estáveis e previsíveis
* Pronto para crescimento SaaS

---

## Estrutura Base (/app)

```
app/
 ├── Domain/
 │    ├── Trade/
 │    ├── Journal/
 │    └── Metrics/
 │
 ├── Application/
 │    ├── UC01_TradeExecution/
 │    ├── UC02_TradeJournal/
 │    └── Contracts/
 │
 ├── Infrastructure/
 │    ├── Persistence/
 │    ├── MarketData/
 │    └── AI/
 │
 └── Interfaces/
      ├── Http/
      └── Console/
```

---

## Namespaces Oficiais

### Domain

```
App\Domain\Trade\Entities
App\Domain\Trade\ValueObjects
App\Domain\Trade\Aggregates
```

### Application

```
App\Application\UC01_TradeExecution\Commands
App\Application\UC01_TradeExecution\Handlers
App\Application\Contracts
```

### Infrastructure

```
App\Infrastructure\Persistence\Repositories
App\Infrastructure\MarketData
App\Infrastructure\AI
```

### Interfaces (Laravel Controllers)

```
App\Interfaces\Http\Controllers
App\Interfaces\Http\Requests
```

---

## Service Providers

Criar providers específicos:

```
app/Providers/DomainServiceProvider.php
app/Providers/ApplicationServiceProvider.php
app/Providers/InfrastructureServiceProvider.php
```

Responsabilidades:

* Bind de interfaces (Application → Infrastructure)
* Registro de Event Listeners

---

## Onde ficam os Models Eloquent

```
app/Infrastructure/Persistence/Eloquent
```

Nunca em Domain.

---

## Onde ficam Migrations

```
database/migrations
```

Referenciam apenas Models da Infrastructure.

---

## Onde ficam DTOs

```
App\Application\*\DTOs
```

Nunca reutilizados fora da Application.

---

## Rotas

```
routes/api.php
```

Controllers chamam apenas Handlers.

---

## Observações Críticas

* Nenhuma classe do Domain depende de Laravel
* Application não conhece Eloquent
* Infrastructure conhece tudo abaixo dela
* Interfaces são descartáveis
