# Roadmap — Trading System

## Visão Geral

Este roadmap define a execução incremental do Trading System, respeitando
a hierarquia de autoridade do projeto:

```
RULES.md > Governança de Risco > ARCHITECTURE.md > CONTEXT.md > Skills
```

Cada fase produz valor verificável, é testável de forma isolada e
não introduz dependências que violem a Clean Architecture.

**Premissa**: A Fase 0 (infraestrutura Docker, Laravel 12, estrutura de pastas,
contratos base) já foi concluída.

---

## Resumo das Fases

| Fase | Nome | Dependência | Foco |
|------|------|-------------|------|
| 1 | Domain Layer | — | Regras de negócio puras |
| 2 | Application Layer | Fase 1 | CQRS, orquestração |
| 3 | Persistence | Fase 2 | Migrations, repositórios, Event Bus |
| 4 | Market Data & Análise Técnica | Fase 2 | Dados B3, indicadores, Trend+Pullback |
| 5 | API REST | Fases 2, 3 | Controllers, validação, Sanctum |
| 6 | Risk Engine & Kill Switch | Fases 1, 3 | Governança de risco operacional |
| 7 | KPIs & Métricas | Fases 3, 5 | Performance, comportamento, processo |
| 8 | IA Learning Loop & RAG | Fases 3, 7 | pgvector, embeddings, RAG, patterns, feedback |
| 9 | Segurança, Compliance & Testes E2E | Todas anteriores | LGPD, auditoria, cobertura |
| 10 | Evolução SaaS | Todas anteriores | Multi-tenant, permissões, deploy |

---

## Fase 1 — Domain Layer ✅

**Objetivo**: Implementar todas as regras de negócio como código puro PHP,
sem nenhuma dependência de framework, banco de dados ou IA.

**Princípio**: O Domain é 100% testável com `pest` sem Laravel boot.

**Resultado**: 136 testes | 278 assertions | PHPStan nível 5 com 0 erros | 12 testes de arquitetura

### 1.1 Shared Kernel

- [x] `DomainEvent` interface (refatorada: eventId, aggregateId, timestamp imutável)
- [x] `Entity` base abstrata (identity, equality)
- [x] `ValueObject` base abstrata (immutability, equality)
- [x] `AggregateRoot` base abstrata (event recording: recordEvent, releaseEvents)

### 1.2 Bounded Context: Trade

**Entities:**
- [x] `Trade` — identidade do trade no ciclo de vida

**Aggregate Root:**
- [x] `TradeAggregate` — orquestra o ciclo completo do trade

**Value Objects:**
- [x] `Price` (valor decimal positivo via bcmath, aritmética safe)
- [x] `Timeframe` (enum: D1, H4, H1, M15)
- [x] `TradeDirection` (enum: LONG | SHORT)
- [x] `PriceLevel` (Price + PriceLevelType: ENTRY | STOP | TARGET)
- [x] `DecisionStatus` (enum: ALLOW | BLOCK | WAIT)
- [x] `Reason` (code + description, validados)
- [x] `Asset` (symbol uppercase + market)

**State Machine:**
- [x] `TradeState` enum (CREATED → ANALYZED → RISK_VALIDATED → APPROVED → EXECUTED → CLOSED)
- [x] Estados terminais: BLOCKED, EXPIRED
- [x] Transições proibidas documentadas e enforced via `canTransitionTo()` / `transitionTo()`
- [x] Invariantes: sem execução sem APPROVED, sem reavaliação após BLOCKED

**Domain Events:**
- [x] `TradeCreated` (assetSymbol, direction, timeframe)
- [x] `TradeAnalyzed` (entry, stop, target prices)
- [x] `TradeRiskValidated` (riskPercentage, positionSize)
- [x] `TradeApproved`
- [x] `TradeBlocked` (reasons[])
- [x] `TradeExecuted` (executedPrice, quantity)
- [x] `TradeClosed` (result como string bcmath)
- [x] `TradeExpired` (reason)

### 1.3 Bounded Context: Journal

**Entities:**
- [x] `TradeJournal` — registro completo do trade executado

**Aggregate Root:**
- [x] `TradeRecord` — agrega execução + resultado + comportamento + lições

**Value Objects:**
- [x] `Money` (amount bcmath + currency BRL, aritmética safe, validação de moeda)
- [x] `Quantity` (inteiro positivo)
- [x] `DateRange` (start + end, imutável, durationInDays)
- [x] `EmotionalState` enum (CONTROLLED | ANXIOUS | IMPULSIVE | OVERCONFIDENT + isDivergent)
- [x] `ResultType` enum (GAIN | LOSS | BREAKEVEN)
- [x] `TradeRationale` (followedPlan, deviationReason obrigatório se !followedPlan)
- [x] `TradeOutcome` (grossResult, netResult, resultType, realizedRR)
- [x] `TradeLesson` (keepDoing, improveNextTime — ambos obrigatórios)

**Regras de Domínio:**
- [x] Trade não fecha sem lição registrada (review obrigatória)
- [x] Emoção divergente + loss = flag de atenção (`hasAttentionFlag()`)
- [x] Execução fora do plano não invalida trade, invalida setup (`hasSetupInvalidation()`)

**Domain Events:**
- [x] `TradeRecordCreated` (recordId, tradeId, assetSymbol)
- [x] `TradeReviewed` (resultType, followedPlan, emotionalState)
- [x] `LearningDataAvailable` (refatorado: eventId, aggregateId)

### 1.4 Bounded Context: Metrics

**Entities:**
- [x] `TraderMetrics` — snapshot consolidado de KPIs (updatePerformance, updateBehavioral)

**Value Objects:**
- [x] `KPI` (name, value bcmath, period)
- [x] `WinRate` (0-1, asPercentage), `Expectancy` (pode ser negativo), `ProfitFactor` (≥0), `MaxDrawdown` (0-1)
- [x] `PlanDisciplineScore` (0-1), `EmotionalStabilityIndex` (0-1)

### 1.5 Testes da Fase 1

- [x] Testes unitários para cada Value Object (criação, validação, igualdade)
- [x] Testes unitários para cada Entity (invariantes, regras)
- [x] Testes da State Machine (18 testes: 8 transições válidas + proibidas + terminais)
- [x] Testes do AggregateRoot (recording de eventos)
- [x] **Zero dependência de Laravel nos testes de Domain**
- [x] Testes de arquitetura com `pest-plugin-arch` (12 testes):
  - Domain não depende de Infrastructure
  - Domain não depende de Application
  - Domain não depende de Interfaces
  - Domain não usa classes do Laravel
  - Value Objects são final
  - Events implementam DomainEvent
  - Aggregates extendem AggregateRoot

### Critérios de Aceite

- [x] Nenhuma classe do Domain importa `Illuminate\*`
- [x] Todos os testes passam sem boot do Laravel
- [x] PHPStan nível 5 com 0 erros (baseline eliminada)
- [x] State machine cobre 100% das transições

---

## Fase 2 — Application Layer (CQRS) ✅

**Objetivo**: Implementar a orquestração dos use cases via CQRS,
usando apenas contratos (interfaces) para comunicação com Infrastructure.

**Resultado**: 55 novos testes (30 UC01 + 12 UC02 + 13 arch) | 154 novas assertions | PHPStan nível 5 com 0 erros

### 2.0 Refatoração de Contracts

- [x] `TradeJournalRepository` refatorado para trabalhar com `TradeRecord` (aggregate), não `TradeJournal` (entity)
- [x] `MetricsRepository` refatorado com `userId` + `period` params e retorno nullable
- [x] `UuidGenerator` contract criado (geração de IDs desacoplada)
- [x] `UserRiskProfileProvider` contract criado
- [x] `UserRiskProfileDTO` compartilhado criado

### 2.1 UC-01: Trade Execution

**Commands (1 por método do aggregate, `final readonly`):**
- [x] `CreateTradeCommand` → `CreateTradeHandler` (retorna `TradeCreatedDTO`)
- [x] `AnalyzeTradeCommand` → `AnalyzeTradeHandler`
- [x] `ValidateTradeRiskCommand` → `ValidateTradeRiskHandler`
- [x] `ApproveTradeCommand` → `ApproveTradeHandler`
- [x] `BlockTradeCommand` → `BlockTradeHandler`
- [x] `ExecuteTradeCommand` → `ExecuteTradeHandler`
- [x] `CloseTradeCommand` → `CloseTradeHandler`
- [x] `ExpireTradeCommand` → `ExpireTradeHandler`

**Queries (`final readonly`):**
- [x] `GetTradeByIdQuery` → `GetTradeByIdHandler` (retorna `TradeViewDTO`)
- [x] `GetOpenTradesQuery` → `GetOpenTradesHandler` (retorna `TradeViewDTO[]`)

**DTOs (`final readonly`):**
- [x] `TradeCreatedDTO` (tradeId)
- [x] `TradeViewDTO` (representação completa do trade)

**Deferido para Fase 4+:**
- `DecideTradeOpportunityCommand` (requer MarketDataProvider + TechnicalAnalysisService)
- `GetMarketDataQuery`, `GetTechnicalAnalysisQuery` (requer infra de market data)
- `MarketDataProvider`, `TechnicalAnalysisService`, `RiskValidationService` contracts

### 2.2 UC-02: Trade Journal

**Commands (`final readonly`):**
- [x] `RegisterTradeExecutionCommand` → `RegisterTradeExecutionHandler` (retorna `TradeRecordCreatedDTO`)
  - Cross-BC validation: verifica trade em estado EXECUTED antes de criar journal
- [x] `ReviewTradeCommand` → `ReviewTradeHandler`
  - Publica TradeReviewed + LearningDataAvailable

**Queries (`final readonly`):**
- [x] `GetTradeRecordByIdQuery` → `GetTradeRecordByIdHandler`
- [x] `GetTradeRecordByTradeIdQuery` → `GetTradeRecordByTradeIdHandler`
- [x] `GetTradeJournalQuery` → `GetTradeJournalHandler` (com filtro por período)
- [x] `GetPerformanceSummaryQuery` → `GetPerformanceSummaryHandler` (retorna zeros se sem métricas)

**DTOs (`final readonly`):**
- [x] `TradeRecordCreatedDTO` (tradeRecordId)
- [x] `TradeRecordViewDTO` (representação completa do trade record)
- [x] `PerformanceSummaryDTO` (snapshot de KPIs)

**Deferido para Fase 8:**
- `GetBehavioralPatternsQuery` (requer IA/RAG)

### 2.3 Padrão de Handler

Todos os handlers seguem o padrão explícito:
1. Carregar aggregate via repository contract
2. Invocar método do domínio (tradução primitivos → Value Objects)
3. Salvar via repository contract
4. `releaseEvents()` → publicar cada evento via `EventPublisher`

### 2.4 Testes da Fase 2

- [x] 18 testes unitários dos Handlers UC01 (com Mockery para contracts)
- [x] 12 testes unitários dos Handlers UC02 (com Mockery para contracts)
- [x] Testes de cenários negativos (transição inválida, trade não-EXECUTED, review duplicado)
- [x] 13 testes de arquitetura Application Layer:
  - Application não importa `Illuminate\*`
  - Application não depende de `App\Infrastructure` ou `App\Interfaces`
  - Commands e Queries são `final readonly`
  - DTOs são `final readonly`
  - Handlers são `final`
- [x] **Zero dependência de Laravel na Application Layer**

### Critérios de Aceite

- [x] Todos os Handlers testados com mocks (30 testes)
- [x] Nenhum import de Infrastructure na Application
- [x] DTOs são imutáveis (`final readonly`)
- [x] Commands retornam `void` (exceto Create que retorna DTO com ID)
- [x] Queries nunca alteram estado
- [x] PHPStan nível 5 com 0 erros
- [x] Pint formatação OK

---

## Fase 3 — Infrastructure: Persistence ✅

**Objetivo**: Implementar a camada de persistência com PostgreSQL 18,
Eloquent Models e repositórios concretos.

**Concluída**: 2026-02-08 | 25 testes de integração (80 assertions) + 186 unit tests (422 assertions)

### 3.0 Refatoração Domain: userId + reconstitute()

- [x] `Trade` — `userId` como 2º param + `reconstitute()` estático
- [x] `TradeAggregate` — `userId` em `create()` + `reconstitute(Trade)`
- [x] `TradeJournal` — `userId` como 2º param + `reconstitute()` estático
- [x] `TradeRecord` — `userId` em `create()` + `reconstitute(TradeJournal)`
- [x] `TradeRepository::getOpenTrades(string $userId)` — filtro por usuário
- [x] Todos os 186 testes unitários atualizados e passando

### 3.1 Migrations

- [x] `create_trade_decisions_table` (state machine completa)
- [x] `create_trade_records_table` (journal com review)
- [x] `create_trader_metrics_table` (6 KPIs)
- [x] `create_domain_events_table` (event store imutável, JSONB payload)
- [x] `create_ai_analyses_table` (pgvector 1536, HNSW index)

### 3.2 Eloquent Models

- [x] `TradeDecisionModel` — casts: immutable_datetime, integer
- [x] `TradeRecordModel` — casts: boolean, immutable_datetime
- [x] `TraderMetricsModel` — casts: immutable_datetime
- [x] `DomainEventModel` — timestamps=false, payload→array
- [x] `AiAnalysisModel` — metadata→array

### 3.3 Repository Implementations (Dehydration/Hydration)

- [x] `TradeRepositoryEloquent` — save/getById/getOpenTrades(userId)
- [x] `TradeJournalRepositoryEloquent` — save/getById/getByTradeId/getByUserId
- [x] `MetricsRepositoryEloquent` — update(upsert)/getCurrent

### 3.4 Event Bus + Services

- [x] `LaravelEventPublisher` — persiste no Event Store + despacha via Laravel events
- [x] `LaravelUuidGenerator` — Str::uuid()

### 3.5 Bindings no InfrastructureServiceProvider

- [x] TradeRepository → TradeRepositoryEloquent
- [x] TradeJournalRepository → TradeJournalRepositoryEloquent
- [x] MetricsRepository → MetricsRepositoryEloquent
- [x] EventPublisher → LaravelEventPublisher
- [x] UuidGenerator → LaravelUuidGenerator

### 3.6 Testes

- [x] 9 testes integração: TradeRepositoryEloquent (save, análise, risco, execução, close, open trades, isolamento, not found, upsert)
- [x] 6 testes integração: TradeJournalRepositoryEloquent (byId, byTradeId, byUserId, período, review, not found)
- [x] 4 testes integração: MetricsRepositoryEloquent (save/get, null, upsert, período)
- [x] 4 testes integração: LaravelEventPublisher (persistência, JSONB, múltiplos, dispatch)
- [x] 2 testes integração: LaravelUuidGenerator (válido, únicos)
- [x] 8 arch tests: Infrastructure (strict_types, final, dependências)

### Critérios de Aceite

- [x] Todas as migrations rodam sem erro (migrate:fresh)
- [x] Repositórios passam nos testes de integração com PostgreSQL real
- [x] Event Store persiste eventos imutáveis com JSONB
- [x] Bindings resolvem corretamente no container
- [x] PHPStan nível 5 com 0 erros
- [x] Pint formatação OK (170 arquivos)

---

## Fase 4 — Infrastructure: Market Data & Análise Técnica

**Objetivo**: Integrar dados de mercado da B3 e implementar o engine
de análise técnica (Trend + Pullback).

### 4.1 Market Data Provider

- [ ] `B3ApiClient` — client HTTP para dados da B3
- [ ] `HistoricalDataProvider` — acesso a dados históricos
- [ ] Normalização de dados (OHLCV padronizado)
- [ ] Cache strategy (Redis para dados recentes)
- [ ] Fallback sources (múltiplas fontes de dados)
- [ ] Validação de qualidade (gaps, outliers, latência)

### 4.2 Indicadores Técnicos

- [ ] `EMA` (Exponential Moving Average) — 21, 50, 200 períodos
- [ ] `RSI` (Relative Strength Index) — 14 períodos
- [ ] `Volume` analysis (comparação com média)
- [ ] Signal candle detection (rejection, engulfing)

### 4.3 Trend + Pullback Strategy

- [ ] `TrendAnalyzer` — identifica direção via EMA 21/50/200
  - Preço acima de EMA 21 e EMA 50
  - EMA 21 acima de EMA 50
- [ ] `PullbackDetector` — detecta retrações válidas
  - Retração até EMA 21 ou 50
  - RSI entre 40-55
  - Volume inferior ao impulso
- [ ] `TrendPullbackStrategy` — Domain Service que consolida análise
- [ ] Entry trigger: candle de força ou rompimento

### 4.4 Risk Calculator

- [ ] Cálculo de position size baseado em risco fixo
- [ ] Stop no fundo do pullback
- [ ] Validação de R:R mínimo
- [ ] Validação de exposição total

### 4.5 Testes da Fase 4

- [ ] Testes unitários de cada indicador (dados conhecidos → resultado esperado)
- [ ] Testes do TrendAnalyzer com séries históricas reais
- [ ] Testes do PullbackDetector
- [ ] Testes de integração do MarketDataProvider (mocked HTTP)
- [ ] Testes de qualidade de dados (gaps, normalização)

### Critérios de Aceite

- [ ] Indicadores produzem resultados corretos para dados conhecidos
- [ ] Strategy é determinística (mesma entrada → mesma saída)
- [ ] Market data normalizado e versionado
- [ ] Cache funciona corretamente
- [ ] Dados brutos nunca são alterados (imutabilidade)

---

## Fase 5 — Interfaces: API REST

**Objetivo**: Criar os endpoints HTTP da API, com validação,
autenticação (Sanctum) e respostas padronizadas.

### 5.1 Autenticação

- [ ] Laravel Sanctum configurado para API tokens
- [ ] Middleware de autenticação nas rotas protegidas
- [ ] Rate limiting por usuário

### 5.2 Endpoints UC-01 (Análise & Decisão)

```
POST   /api/v1/analysis/{symbol}         → Analisar ativo
GET    /api/v1/analysis/opportunities     → Listar oportunidades
GET    /api/v1/analysis/{id}              → Detalhe de uma análise
GET    /api/v1/assets                     → Listar ativos disponíveis
```

**Controllers:**
- [ ] `AnalysisController`
- [ ] `AssetController`

**Requests:**
- [ ] `AnalyzeAssetRequest` (validação)

### 5.3 Endpoints UC-02 (Journal)

```
POST   /api/v1/trades/{decisionId}/execute    → Registrar execução
PUT    /api/v1/trades/{recordId}/close         → Fechar trade (com lições)
GET    /api/v1/trades/journal                  → Listar journal
GET    /api/v1/trades/{recordId}               → Detalhe do trade
PATCH  /api/v1/trades/{recordId}/notes         → Adicionar nota
```

**Controllers:**
- [ ] `TradeExecutionController`
- [ ] `TradeJournalController`

**Requests:**
- [ ] `RegisterExecutionRequest`
- [ ] `CloseTradeRequest` (valida lição obrigatória)

### 5.4 Endpoints de Perfil & Métricas

```
GET    /api/v1/profile                    → Perfil do trader
PUT    /api/v1/profile/risk               → Atualizar perfil de risco
GET    /api/v1/metrics/performance        → KPIs de performance
GET    /api/v1/metrics/behavioral         → KPIs comportamentais
```

### 5.5 Respostas Padronizadas

- [ ] API Resource classes para transformação consistente
- [ ] Error handling padronizado (JSON API format)
- [ ] Pagination para listagens

### 5.6 Testes da Fase 5

- [ ] Feature tests de cada endpoint (happy path + error cases)
- [ ] Testes de autenticação (com e sem token)
- [ ] Testes de validação de request
- [ ] Testes de rate limiting

### Critérios de Aceite

- [ ] Todos os endpoints documentados e testados
- [ ] Controllers são thin (delegam para Handlers)
- [ ] Nenhuma lógica de negócio nos Controllers
- [ ] Respostas de erro consistentes e informativas

---

## Fase 6 — Risk Engine & Kill Switch

**Objetivo**: Implementar a governança de risco operacional como definido
em RULES.md e no skill `risk-governance.md`. Esta fase é **crítica** —
o sistema não pode operar sem ela.

### 6.1 Regras de Risco Globais

- [ ] Risco máximo por trade (0.5% - 2% do capital)
- [ ] Risco máximo diário
- [ ] Drawdown máximo global
- [ ] Máximo de trades simultâneos
- [ ] Limite por ativo
- [ ] Limite por mercado

### 6.2 Risk Engine

- [ ] `RiskEngine` — valida se operação é permitida antes de qualquer trade
- [ ] Se Risk Engine rejeita → operação **não existe**
- [ ] Cálculo de position size automático
- [ ] Validação de stop obrigatório
- [ ] Validação de R:R mínimo

### 6.3 Kill Switch

**Manual:**
- [ ] Endpoint `POST /api/v1/risk/kill-switch` — ativação manual
- [ ] Interface para admin/trader ativar

**Automático:**
- [ ] Trigger por violação de regra de risco
- [ ] Trigger por drawdown máximo atingido
- [ ] Trigger por número de losses consecutivos

**Emergência:**
- [ ] Trigger por falha de infraestrutura
- [ ] Health check → kill switch

**Ações do Kill Switch:**
- [ ] Cancela novas operações
- [ ] Gera alerta crítico
- [ ] Log imutável do evento
- [ ] Notificação ao trader

### 6.4 Monitoramento de Exposição

- [ ] Exposição total em tempo real
- [ ] Exposição por ativo
- [ ] Exposição por mercado
- [ ] Alerta ao se aproximar dos limites

### 6.5 Testes da Fase 6

- [ ] Testes de cada regra de risco isoladamente
- [ ] Testes do Kill Switch (ativação e desativação)
- [ ] Testes de integração (trade bloqueado quando limite atingido)
- [ ] Testes de stress (múltiplas operações simultâneas)

### Critérios de Aceite

- [ ] Nenhum trade passa sem validação do Risk Engine
- [ ] Kill Switch funciona nos 3 modos (manual, automático, emergência)
- [ ] Logs de risco são imutáveis
- [ ] Sistema pode ser parado em segundos

---

## Fase 7 — KPIs & Métricas

**Objetivo**: Implementar o cálculo e armazenamento dos KPIs definidos
em `trader-kpis.md`, alimentados pelo Journal (UC-02).

### 7.1 KPIs de Performance

- [ ] **Win Rate** — % de trades vencedores
- [ ] **Risk/Reward Médio** — R:R realizado médio
- [ ] **Expectancy** — (WR × AvgGain) − (LR × AvgLoss)
- [ ] **Profit Factor** — soma ganhos ÷ soma perdas
- [ ] **Max Drawdown** — maior declínio acumulado de capital
- [ ] **Consistência Mensal** — % de meses positivos

### 7.2 KPIs Comportamentais

- [ ] **Plan Discipline Score** — % trades executados conforme plano
- [ ] **Setup Fidelity** — aderência ao setup Trend+Pullback
- [ ] **Emotional Stability Index** — correlação emoção × resultado
- [ ] **Overtrading Index** — frequência de trades fora do plano
- [ ] **Loss Behavior Score** — comportamento após trades negativos

### 7.3 KPIs de Processo

- [ ] **Decision vs Execution Deviation** — diferença aprovado vs executado
- [ ] **Setup Validity Decay** — taxa de setups aprovados que expiram

### 7.4 Cálculo e Armazenamento

- [ ] Serviço de cálculo de KPIs (event-driven, recalcula ao fechar trade)
- [ ] Snapshot de métricas por período (diário, semanal, mensal)
- [ ] Read Models otimizados para consulta
- [ ] Cache de métricas recentes (Redis)

### 7.5 Testes da Fase 7

- [ ] Testes unitários de cada fórmula de KPI
- [ ] Testes com dados conhecidos (resultado esperado calculado manualmente)
- [ ] Testes de edge cases (zero trades, 100% win, 100% loss)

### Critérios de Aceite

- [ ] Fórmulas conferem com cálculo manual
- [ ] KPIs atualizados automaticamente ao fechar trade
- [ ] Dados disponíveis via API (endpoints da Fase 5)
- [ ] Snapshots versionados e auditáveis

---

## Fase 8 — IA Learning Loop & RAG (Retrieval-Augmented Generation)

**Objetivo**: Implementar a IA como **observador e sistema de feedback**,
usando dados do Journal e KPIs para melhorar decisões futuras.
Toda análise gerada pela IA é armazenada como **embedding vetorial** (pgvector)
para ser recuperada por **similarity search** em análises futuras (padrão RAG).

**Regra inviolável**: IA NÃO executa trades, NÃO aprova, NÃO altera dados.

### 8.1 Estratégia de Embeddings & RAG

A IA produz análises textuais que são convertidas em vetores e armazenadas
no PostgreSQL via pgvector. Quando uma nova análise é solicitada, o sistema
busca análises passadas similares e as injeta como contexto.

```
┌─────────────┐     ┌──────────────────┐     ┌──────────────┐
│  AI gera    │────▶│ Embedding Model  │────▶│  pgvector    │
│  análise    │     │ (texto → vetor)  │     │  armazena    │
└─────────────┘     └──────────────────┘     └──────┬───────┘
                                                     │
┌─────────────┐     ┌──────────────────┐     ┌──────▼───────┐
│  AI recebe  │◀────│ Contexto RAG     │◀────│  Similarity  │
│  contexto   │     │ (top-N similar)  │     │  Search      │
└─────────────┘     └──────────────────┘     └──────────────┘
```

**Tipos de conteúdo armazenado como embedding:**

| Tipo | Exemplo | Valor para RAG |
|------|---------|----------------|
| Análise de trade | "PETR4 tendência alta, pullback MA21..." | Encontrar trades similares |
| Journal / lições | "Entrei cedo demais no pullback..." | Padrões de erro recorrentes |
| Decisão de risco | "BLOCK: exposição setorial > 30%" | Decisões de risco similares |
| Feedback da IA | "Trader tende a ignorar stop em VALE3" | Padrões comportamentais |
| Contexto de mercado | "IBOV em consolidação, volatilidade baixa" | Regimes de mercado similares |

**Modelo de embedding (escolher um):**

| Modelo | Dimensões | Custo | Observação |
|--------|-----------|-------|------------|
| OpenAI `text-embedding-3-small` | 1536 | Pago (~$0.02/1M tokens) | Melhor custo-benefício |
| OpenAI `text-embedding-3-large` | 3072 | Pago | Maior precisão |
| Ollama `nomic-embed-text` (local) | 768 | Grátis | Sem dependência externa |

> **Decisão de dimensões é irreversível sem re-embedar todo o histórico.**
> Escolher antes de iniciar a implementação.

### 8.2 Embedding Pipeline

- [ ] `EmbeddingService` contract (Application layer)
  - `generateEmbedding(string $text): EmbeddingVector`
  - `generateBatchEmbeddings(array $texts): array`
- [ ] `OpenAIEmbeddingService` implementação (Infrastructure layer)
- [ ] `OllamaEmbeddingService` implementação alternativa (opcional, local)
- [ ] `EmbeddingVector` Value Object no Domain (Shared)
  - Encapsula o vetor de dimensões
  - Validação de dimensões no construtor
- [ ] Listener `GenerateAnalysisEmbedding` que escuta eventos:
  - `TradeAnalyzed` → embeda a análise
  - `TradeClosed` → embeda o journal + lição
  - `LearningDataAvailable` → embeda o feedback
- [ ] Geração de embedding é **assíncrona** (event-driven, não bloqueia fluxo)
- [ ] Fallback: se embedding falhar, análise continua sem RAG

### 8.3 RAG — Retrieval-Augmented Generation

- [ ] `AiAnalysisRepository` contract (Application layer)
  - `save(AiAnalysis $analysis): void`
  - `findSimilar(EmbeddingVector $query, int $limit, ?string $type): AiAnalysis[]`
  - `findByTradeId(string $tradeId): AiAnalysis[]`
- [ ] `AiAnalysisRepositoryPgVector` implementação (Infrastructure layer)
  - Usa operador `<=>` (cosine distance) do pgvector
  - Índice HNSW para performance em escala
- [ ] `RagContextBuilder` (Infrastructure/AI)
  - Antes de gerar nova análise, busca top-N análises similares
  - Formata como contexto para o prompt
  - Filtrável por tipo (trade, journal, risco, feedback)
- [ ] Integração com PromptBuilders:
  - Prompt inclui seção "Análises históricas similares"
  - Prompt indica claramente o que é contexto RAG vs. dados atuais

**Exemplo de query RAG:**

```sql
-- Buscar as 5 análises mais similares à situação atual
SELECT id, trade_id, content, analysis_type, metadata,
       1 - (embedding <=> $1) AS similarity
FROM ai_analyses
WHERE analysis_type = 'trade_analysis'
  AND (metadata->>'timeframe') = 'D1'
ORDER BY embedding <=> $1
LIMIT 5;
```

### 8.4 Data Pipeline

- [ ] Captura de Learning Snapshots (trades, KPIs, emoções)
- [ ] Feature extraction para IA
- [ ] Versionamento de dados de treino
- [ ] Metadata enriquecido em JSONB (ticker, timeframe, strategy, regime de mercado)

### 8.5 Pattern Detection

- [ ] Correlação setup × resultado (via similarity search)
- [ ] Correlação emoção × loss
- [ ] Identificação de erros recorrentes (cluster de embeddings similares)
- [ ] Detecção de sequências fora do plano

### 8.6 Bias Detection

- [ ] Overconfidence (excesso de confiança pós-ganho)
- [ ] Revenge trading (operação impulsiva pós-loss)
- [ ] Overtrading (frequência acima do normal)

### 8.7 Feedback Generation

- [ ] Feedback operacional: "Você perde mais quando ignora o stop"
- [ ] Alertas preventivos: "Risco comportamental elevado hoje"
- [ ] Recomendações de processo: "Reduza frequência", "Pause após X losses"
- [ ] **Feedback enriquecido por RAG**: "Em situações similares, você obteve X% de perda"

### 8.8 Integração com UC-01 (RAG-Powered)

- [ ] Antes de nova análise, buscar análises similares via pgvector
- [ ] Injetar contexto RAG no prompt da IA
- [ ] Exemplo: "Em 3 situações similares com PETR4 em pullback, 2 resultaram em gain"
- [ ] IA não bloqueia — apenas informa com contexto histórico

### 8.9 Integração com Laravel AI SDK

- [ ] Prompts versionados em `Infrastructure/AI/PromptBuilders/`
- [ ] IA consumida apenas via Infrastructure layer
- [ ] Outputs logados e rastreáveis
- [ ] Feature flags para desabilitar IA sem impacto
- [ ] Feature flag separada para RAG (pode desligar RAG mantendo IA básica)

### 8.10 Testes da Fase 8

- [ ] Testes unitários do `EmbeddingVector` Value Object
- [ ] Testes do `EmbeddingService` (mock do provider externo)
- [ ] Testes de integração do `AiAnalysisRepositoryPgVector` (similarity search real)
- [ ] Testes do `RagContextBuilder` (formatação do contexto)
- [ ] Testes de pattern detection com dados sintéticos
- [ ] Testes de feedback generation
- [ ] Testes que IA nunca altera dados do Journal
- [ ] Testes que IA pode ser desligada sem quebrar o sistema
- [ ] Testes que RAG pode ser desligado independentemente da IA
- [ ] Testes de arquitetura: IA e embeddings apenas em Infrastructure

### Critérios de Aceite

- [ ] IA funciona como observador puro
- [ ] Todos os outputs da IA são logados
- [ ] IA desligável e reversível (feature flag)
- [ ] RAG desligável independentemente (feature flag separada)
- [ ] Prompts versionados como código
- [ ] Sistema funciona 100% sem IA e sem RAG
- [ ] Embeddings gerados assincronamente (não bloqueiam fluxo)
- [ ] Similarity search retorna resultados relevantes (validado manualmente)
- [ ] Dimensões do vetor documentadas como ADR (decisão irreversível)

---

## Fase 9 — Segurança, Compliance & Testes E2E

**Objetivo**: Garantir que o sistema atende aos requisitos de segurança,
compliance (LGPD) e qualidade definidos em RULES.md.

### 9.1 Segurança (OWASP)

- [ ] Input validation em todas as fronteiras
- [ ] SQL injection prevention (prepared statements)
- [ ] XSS prevention (output encoding)
- [ ] CSRF protection (API stateless)
- [ ] Rate limiting configurado
- [ ] Headers de segurança (CORS, CSP, etc.)

### 9.2 Dados Sensíveis

- [ ] Secrets apenas via `.env` / Vault
- [ ] Logs nunca expõem dados sensíveis
- [ ] Encryption at rest para dados financeiros
- [ ] Encryption in transit (TLS)

### 9.3 Compliance LGPD

- [ ] Dados pessoais identificados e catalogados
- [ ] Consentimento rastreável
- [ ] Direito ao esquecimento implementável
- [ ] Data retention policies definidas

### 9.4 Auditoria

- [ ] Event Store imutável e completo
- [ ] Trail de decisão: análise → aprovação → execução → resultado
- [ ] Timestamp com precisão em todos os eventos
- [ ] Retenção configurável

### 9.5 Testes End-to-End

- [ ] UC-01 completo: request → análise → decisão → response
- [ ] UC-02 completo: registro → execução → close com lição → KPI atualizado
- [ ] Fluxo de risco: trade bloqueado por Risk Engine
- [ ] Kill Switch: ativação → bloqueio de operações
- [ ] IA feedback loop: trade fechado → pattern detected → feedback gerado

### 9.6 Testes de Arquitetura (pest-plugin-arch)

- [ ] Domain não depende de Infrastructure, Application, Laravel
- [ ] Application não depende de Infrastructure, Laravel
- [ ] Infrastructure não depende de Interfaces
- [ ] Controllers não contêm lógica de negócio
- [ ] Comunicação entre Bounded Contexts apenas via eventos

### 9.7 Cobertura

- [ ] Domain: > 90%
- [ ] Application: > 85%
- [ ] Infrastructure: > 70%
- [ ] Feature tests: todos os endpoints cobertos

### Critérios de Aceite

- [ ] Zero vulnerabilidades OWASP Top 10
- [ ] Testes E2E passam para ambos os UCs
- [ ] Audit trail completo e verificável
- [ ] Testes de arquitetura passam (zero violações)
- [ ] LGPD checklist verificado

---

## Fase 10 — Evolução SaaS

**Objetivo**: Preparar o sistema para múltiplos usuários (multi-tenant),
com isolamento de dados, permissões e deployment containerizado.

### 10.1 Multi-tenancy

- [ ] Isolamento de dados por tenant (user)
- [ ] Scoped queries por tenant
- [ ] Validação de cross-tenant access (bloqueado)
- [ ] Configuração por tenant (risk profile, strategy)

### 10.2 Permissões & RBAC

- [ ] Roles: trader, admin
- [ ] Permissões granulares por recurso
- [ ] Audit de acesso por tenant

### 10.3 CI/CD Pipeline ✅ (antecipado — implementado na Fase 0)

- [x] GitHub Actions: lint (Pint), static analysis (PHPStan/Larastan), testes, coverage
- [x] Pipeline: PR → lint → analyse → unit tests → arch tests → integration → feature → coverage
- [x] Security audit semanal (composer audit)
- [x] Deploy workflow (staging/production via tags e manual dispatch)
- [x] Documentação completa em `docs/07-cicd/`
- [ ] Configurar branch protection rules no GitHub (manual)
- [ ] Deploy efetivo (depende da escolha de hosting)

### 10.4 Deployment

- [ ] Docker production-ready (multi-stage build)
- [ ] Health checks em todos os serviços
- [ ] Backup automatizado (PostgreSQL)
- [ ] Monitoring (APM, error tracking)
- [ ] Runbooks operacionais

### 10.5 Documentação

- [ ] API documentation (OpenAPI / Swagger)
- [ ] Guia de onboarding
- [ ] Architecture Decision Records (ADRs)

### Critérios de Aceite

- [ ] Dados isolados entre tenants (teste de penetração)
- [ ] CI/CD funcional e automatizado
- [ ] Deploy zero-downtime
- [ ] Monitoring ativo e com alertas
- [ ] Documentação completa para onboarding

---

## Regras Transversais (Aplicam a Todas as Fases)

### Qualidade de Código
- Readable > clever
- `declare(strict_types=1)` em todos os arquivos
- Pest para todos os testes
- Pint (Laravel) para formatação
- PHPStan/Larastan nível 5+ (progressão incremental)

### CI/CD (GitHub Actions)
- Todo PR deve passar no CI antes de merge
- Pipeline: lint → static analysis → unit tests → arch tests → integration → feature
- Security audit semanal automático
- Coverage report como artefato

### Versionamento
- Commits semânticos e frequentes
- Branches por fase/feature
- PRs com revisão e CI obrigatório

### Decisões Arquiteturais
- Qualquer desvio de ARCHITECTURE.md deve ser documentado como ADR
- Nenhum "atalho temporário" — se não cabe na arquitetura, não entra

### IA
- IA **nunca** é trader, decisor ou executor
- Outputs da IA são insumos para regras explícitas
- Modelos desligáveis e reversíveis

### Incerteza
- Em caso de dúvida: reduzir exposição
- Em caso de conflito: parar operações
- Não agir é uma decisão válida

---

## Observação Final

> O lucro é consequência. A consistência é processo.
> Este roadmap forma o operador — não promete retornos.

A evolução é **incremental, versionada e auditável**.
Cada fase entrega valor real e verificável.
Nenhuma fase compromete as anteriores.
