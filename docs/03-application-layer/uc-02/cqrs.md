# CQRS – Refinamento do UC-02 (Trade Journal & Learning Loop)

## Objetivo
Separar claramente **intenção (Commands)** de **consulta (Queries)** no acompanhamento pós-trade, garantindo auditabilidade, imutabilidade e suporte a métricas e aprendizado.

---

## Visão Geral do Fluxo
1. Commands registram fatos irreversíveis
2. Queries constroem visões analíticas
3. Write Side protege invariantes
4. Read Side otimiza análise e IA

---

## COMMANDS (Write Side)

### RegisterTradeExecutionCommand
**Responsabilidade:** criar um TradeRecord a partir de uma decisão aprovada

**Dados**
- tradeDecisionId
- entryPrice
- quantity
- entryDate

---

### CloseTradeCommand
**Responsabilidade:** encerrar o trade com resultado e aprendizado

**Dados**
- tradeRecordId
- exitPrice
- exitDate
- followedPlan
- deviationReason
- emotionalState
- lessonsKeep
- lessonsImprove

---

### MarkDecisionAsExpiredCommand
**Responsabilidade:** encerrar decisões não executadas

**Dados**
- tradeDecisionId

---

## COMMAND HANDLERS

### RegisterTradeExecutionHandler
Fluxo:
1. Verifica TradeDecision em estado APPROVED
2. Cria TradeRecord
3. Persiste estado inicial (OPEN)

---

### CloseTradeHandler
Fluxo:
1. Calcula resultado financeiro
2. Classifica resultado
3. Valida lições aprendidas
4. Fecha TradeRecord (CLOSED)

---

## QUERIES (Read Side)

### GetTradeJournalQuery
**Responsabilidade:** listar trades do usuário
- userId
- dateRange

---

### GetTradeDetailsQuery
**Responsabilidade:** obter detalhes completos de um trade
- tradeRecordId

---

### GetPerformanceSummaryQuery
**Responsabilidade:** métricas consolidadas
- userId
- timeframe

**Retorno**
- winRate
- avgRiskReward
- totalPnL
- planDisciplineScore

---

### GetBehavioralPatternsQuery
**Responsabilidade:** detectar padrões comportamentais
- userId

**Retorno**
- emotionVsResult
- deviationFrequency

---

## READ MODELS

- TradeJournalReadModel
- PerformanceSummaryReadModel
- BehavioralInsightReadModel

---

## EVENTOS (Futuro / Opcional)

### TradeClosedEvent
- tradeRecordId
- resultType
- emotionalState

---

## Regras Estruturais
- Commands nunca retornam estado
- Queries nunca alteram dados
- Write Side sempre passa pelo Aggregate
- Read Side pode ser eventualmente consistente

---

## Observação Crítica
Este CQRS transforma trades em **dados confiáveis de aprendizado**, não apenas histórico financeiro.

