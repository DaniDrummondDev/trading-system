# Domínio – Trade Journal: Entidades, Value Objects e Aggregates

## Objetivo
Modelar o núcleo do **Trade Journal** como um subdomínio essencial de aprendizado, garantindo imutabilidade, rastreabilidade e feedback estruturado para evolução do trader.

---

## Aggregate

### TradeRecord (Aggregate Root)
Representa um trade executado e registrado no journal. É a fonte única da verdade pós-execução.

**Identidade**
- tradeRecordId

**Componentes Agregados**
- TradeDecisionRef
- ExecutionDetails
- TradeOutcome
- BehavioralAssessment
- LessonsLearned

**Invariantes**
- Um TradeRecord é criado apenas a partir de uma TradeDecision APPROVED
- Após CLOSED, o TradeRecord é imutável
- Lições aprendidas são obrigatórias para fechamento

---

## Entidades

### TradeDecisionRef
Referência à decisão que originou o trade.
- tradeDecisionId
- assetSymbol
- timeframe

---

### ExecutionDetails
Detalhes reais da execução.
- entryPrice
- exitPrice
- quantity
- entryDate
- exitDate

---

### TradeOutcome
Resultado consolidado do trade.
- grossResult
- netResult
- resultType (GAIN | LOSS | BREAKEVEN)
- riskRewardRealized

---

### BehavioralAssessment
Avaliação comportamental do trader.
- followedPlan (bool)
- deviationReason
- emotionalState

---

### LessonsLearned
Registro explícito de aprendizado.
- keepDoing
- improveNextTime

---

## Value Objects

### Money
- amount
- currency (BRL)

Regras:
- Imutável
- Operações matemáticas seguras

---

### Price
- value

---

### Quantity
- value

---

### DateRange
- start
- end

---

### EmotionalState
- value (CONTROLLED | ANXIOUS | IMPULSIVE | OVERCONFIDENT)

---

### ResultType
- value (GAIN | LOSS | BREAKEVEN)

---

## Regras de Domínio Críticas
- Trade sem lição aprendida não pode ser fechado
- Emoção divergente + prejuízo gera flag de atenção
- Execução fora do plano não invalida o trade, mas invalida o setup

---

## Limites Claros
- Journal não decide trades
- Journal não altera decisões
- Journal não se comunica com infraestrutura

---

## Observação Estratégica
Este Aggregate é a base real para:
- Métricas confiáveis
- Aprendizado assistido por IA
- Evolução consistente no médio e longo prazo
