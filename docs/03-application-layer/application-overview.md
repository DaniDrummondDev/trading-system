# Application Layer – Use Cases e DTOs (MVP)

## Objetivo
Definir a Application Layer responsável por orquestrar os fluxos do domínio, sem conter regras de negócio. Atua como camada intermediária entre Interface (UI/API) e Domínio.

---

## Princípios
- Sem lógica de negócio
- Orquestra Bounded Contexts
- Usa contratos (interfaces)
- Preparada para CQRS

---

## Caso de Uso Principal
### UC-01 – Analisar Ativo e Decidir Operação (Tendência + Pullback)

### Comando
**AnalyzeAssetForTradeCommand**
- assetSymbol
- timeframe (D1)
- userId

### Handler
**AnalyzeAssetForTradeHandler**
Responsabilidades:
1. Solicitar dados ao Market Data Context
2. Solicitar análise técnica ao Analysis Context
3. Solicitar validação ao Risk Management Context
4. Consolidar decisão
5. Retornar resultado para UI

---

## DTOs de Entrada
### AnalyzeAssetInputDTO
- assetSymbol
- timeframe
- userId

---

## DTOs de Saída
### TradeDecisionOutputDTO
- decision (ALLOW | BLOCK | WAIT)
- confidenceLevel
- reasons[]
- suggestedEntry
- suggestedStop
- suggestedTarget

---

## Queries
### GetLastAnalysisQuery
- assetSymbol
- timeframe

### GetUserRiskProfileQuery
- userId

---

## Contratos (Interfaces)

### MarketDataProvider
- getDailyCandles(assetSymbol)

### TechnicalAnalysisService
- analyzeTrend(candles)
- detectPullback(candles)

### RiskValidationService
- validateRisk(entry, stop, userProfile)

---

## Resultado Esperado
A Application Layer retorna uma decisão estruturada, explicável e rastreável, sem executar trades nem tomar decisões automáticas.

---

## Observações
- IA não participa desta camada
- Pronta para simulação, paper trade e SaaS
- Extensível para outros timeframes

