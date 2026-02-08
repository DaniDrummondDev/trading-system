# Application Flow — UC-01

## Use Case

UC-01 — Execução de Trade (Tendência + Pullback)

## Objetivo

Orquestrar a decisão, validação, execução simulada/real e registro inicial de um trade a partir de dados de mercado diários.

## Ator Primário

Trader (usuário operador)

## Camada Application — Responsabilidades

* Orquestrar o fluxo do caso de uso
* Coordenar comandos e queries (CQRS)
* Validar regras de aplicação (não de domínio)
* Persistir resultados via contratos

## Fluxo Principal (Happy Path)

1. Receber comando `OpenTradeCommand`
2. Validar contexto operacional (sessão, ativo, timeframe)
3. Consultar dados de mercado (`GetMarketSnapshotQuery`)
4. Avaliar critérios de tendência
5. Avaliar critérios de pullback
6. Calcular risco, tamanho da posição e R:R
7. Criar Trade (Aggregate Root)
8. Persistir Trade (Repository)
9. Emitir evento `TradeOpened`

## Commands

* OpenTradeCommand
* CancelTradeCommand

## Queries

* GetMarketSnapshotQuery
* GetActiveTradesQuery

## Contratos (Interfaces)

* MarketDataProvider
* TradeRepository
* RiskProfileProvider

## Outputs

* TradeId
* Status do Trade
* Snapshot decisório

## Eventos de Domínio Emitidos

* TradeOpened
* TradeRejected

## Observações Arquiteturais

* Nenhuma dependência de Infra
* Sem regras de negócio complexas (delegadas ao domínio)
