# Estrutura de Pastas — Clean Architecture

## Visão Geral

Estrutura pensada para:

* Evolução para SaaS
* Separação clara de responsabilidades
* Suporte a CQRS, IA e múltiplas fontes de dados

---

## /src

### /Domain

Responsável por regras de negócio puras.

```
Domain/
 ├── Trade/
 │    ├── Entities/
 │    │    └── Trade.php
 │    ├── ValueObjects/
 │    │    ├── Price.php
 │    │    ├── Timeframe.php
 │    │    └── TradeDirection.php
 │    ├── Aggregates/
 │    │    └── TradeAggregate.php
 │    └── Events/
 │         └── TradeClosed.php
 │
 ├── Journal/
 │    ├── Entities/
 │    │    └── TradeJournal.php
 │    ├── ValueObjects/
 │    │    ├── EmotionTag.php
 │    │    └── TradeNote.php
 │    └── Events/
 │         └── TradeReviewed.php
 │
 └── Metrics/
      ├── Entities/
      │    └── TraderMetrics.php
      └── ValueObjects/
           └── KPI.php
```

---

### /Application

Orquestra casos de uso (sem regra de negócio).

```
Application/
 ├── TradeExecution/
 │    ├── Commands/
 │    │    └── OpenTradeCommand.php
 │    ├── Queries/
 │    │    └── GetTradeContextQuery.php
 │    ├── Handlers/
 │    │    └── OpenTradeHandler.php
 │    └── DTOs/
 │         └── TradeDecisionDTO.php
 │
 ├── TradeJournal/
 │    ├── Commands/
 │    │    ├── CloseTradeCommand.php
 │    │    └── AddTradeNoteCommand.php
 │    ├── Queries/
 │    │    └── GetTradeJournalQuery.php
 │    ├── Handlers/
 │    │    └── CloseTradeHandler.php
 │    └── DTOs/
 │         └── TradeReviewDTO.php
 │
 └── Contracts/
      ├── TradeRepository.php
      ├── MetricsRepository.php
      └── LearningEventPublisher.php
```

---

### /Infrastructure

Implementações técnicas.

```
Infrastructure/
 ├── Persistence/
 │    ├── Eloquent/
 │    │    └── TradeModel.php
 │    └── Repositories/
 │         └── TradeRepositoryImpl.php
 │
 ├── MarketData/
 │    ├── B3ApiClient.php
 │    └── HistoricalDataProvider.php
 │
 └── AI/
      ├── PromptBuilders/
      ├── FeatureExtractors/
      └── LearningPipeline.php
```

---

### /Interfaces

Entradas externas.

```
Interfaces/
 ├── Http/
 │    ├── Controllers/
 │    │    └── TradeController.php
 │    └── Requests/
 │         └── OpenTradeRequest.php
 │
 └── Console/
      └── Commands/
```
