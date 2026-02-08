# CQRS – Refinamento do Fluxo UC-01 (Análise e Decisão)

## Objetivo
Separar explicitamente operações de **leitura (Query)** e **intenção/decisão (Command)**, garantindo clareza, rastreabilidade e preparação para escala.

---

## Visão Geral do Fluxo
1. Queries coletam contexto e estado
2. Command consolida intenção
3. Handler coordena domínio
4. Resultado é imutável e auditável

---

## QUERIES (Read Side)

### GetMarketDataQuery
**Responsabilidade:** obter candles diários do ativo
- assetSymbol
- timeframe (D1)

**Retorno:**
- CandleDTO[]

---

### GetTechnicalAnalysisQuery
**Responsabilidade:** avaliar tendência e pullback
- assetSymbol
- timeframe

**Retorno:**
- trendDirection
- pullbackDetected (bool)
- keyLevels

---

### GetUserRiskProfileQuery
**Responsabilidade:** recuperar parâmetros do usuário
- userId

**Retorno:**
- maxRiskPerTrade
- accountSize
- maxDailyLoss

---

### GetOpenExposureQuery
**Responsabilidade:** verificar exposição atual
- userId

**Retorno:**
- currentExposure
- openTradesCount

---

## COMMAND (Write Side)

### DecideTradeOpportunityCommand
**Responsabilidade:** consolidar decisão de trade
- userId
- assetSymbol
- timeframe
- analysisSnapshotId

---

## COMMAND HANDLER

### DecideTradeOpportunityHandler
Fluxo:
1. Valida consistência das queries
2. Solicita validação de risco
3. Aplica regras de bloqueio
4. Gera decisão imutável

---

## OUTPUT

### TradeDecisionResult
- decision (ALLOW | BLOCK | WAIT)
- reasons[]
- riskSummary
- entrySuggestion
- stopSuggestion
- targetSuggestion

---

## EVENTOS (Opcional – futuro)

### TradeDecisionGeneratedEvent
- decisionId
- userId
- assetSymbol
- timestamp

---

## Regras Estruturais
- Queries nunca chamam Commands
- Commands não retornam estado, apenas resultado
- Read models podem ser otimizados
- Write side sempre passa pelo domínio

---

## Benefícios Diretos
- Backtest sem duplicar lógica
- Replay de decisões
- Base sólida para IA explicável
- Preparado para SaaS multiusuário

---

## Observação Final
Nenhuma execução de ordem ocorre aqui. O sistema **decide**, o humano **executa**.

