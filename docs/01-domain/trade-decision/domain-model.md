# Domínio – Entidades, Value Objects e Aggregates (UC-01)

## Objetivo
Extrair e consolidar os elementos centrais do domínio a partir do fluxo de análise e decisão de trade (tendência + pullback).

---

## Aggregates

### TradeDecision (Aggregate Root)
Representa uma decisão única e imutável sobre uma oportunidade de trade.

**Identidade**
- tradeDecisionId

**Componentes**
- Asset
- Timeframe
- AnalysisResult
- RiskAssessment
- DecisionStatus

**Invariantes**
- Uma decisão não pode ser alterada após criada
- Uma decisão sempre possui justificativas
- Uma decisão pertence a um único usuário

---

## Entidades

### User
- userId
- riskProfileId

---

### Asset
- symbol
- market (B3)

---

### AnalysisResult
- trendDirection
- pullbackDetected
- keyLevels
- confidenceScore

---

### RiskAssessment
- maxRiskAllowed
- calculatedRisk
- exposureAfterTrade

---

## Value Objects

### Timeframe
- value (D1)

Regras:
- Imutável
- Validado na criação

---

### PriceLevel
- price
- type (ENTRY | STOP | TARGET)

---

### DecisionStatus
- value (ALLOW | BLOCK | WAIT)

---

### Reason
- code
- description

---

## Relações Importantes
- TradeDecision agrega AnalysisResult e RiskAssessment
- User fornece parâmetros, mas não participa da decisão
- Asset é sempre tratado como VO-identificável

---

## Limites Claros
- Nenhuma entidade conhece infraestrutura
- Nenhuma entidade executa ordens
- IA não existe no domínio

---

## Observação Final
Este modelo prioriza rastreabilidade, auditabilidade e aprendizado progressivo do operador.

