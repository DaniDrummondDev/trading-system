# Application Flow — UC-02

## Use Case

UC-02 — Acompanhamento Pós-Trade (Trade Journal / Learning Loop)

## Objetivo

Registrar execução, resultado e comportamento do trader para análise de performance e retroalimentação da IA.

## Ator Primário

Trader

## Camada Application — Responsabilidades

* Orquestrar registro pós-trade
* Coordenar escrita e leitura de métricas
* Disparar eventos para aprendizado da IA

## Fluxo Principal (Happy Path)

1. Receber comando `CloseTradeCommand`
2. Validar estado do trade
3. Registrar execução (preço, horário, motivo)
4. Calcular métricas do trade
5. Atualizar Trade Journal
6. Persistir KPIs
7. Emitir evento `TradeClosed`
8. Disparar evento `LearningDataAvailable`

## Commands

* CloseTradeCommand
* AddTradeNoteCommand
* TagTradeEmotionCommand

## Queries

* GetTradeJournalQuery
* GetPerformanceMetricsQuery

## Contratos (Interfaces)

* TradeRepository
* TradeJournalRepository
* MetricsRepository
* LearningEventPublisher

## Outputs

* Resultado do trade
* KPIs atualizados
* Registro no Journal

## Eventos de Domínio Emitidos

* TradeClosed
* TradeReviewed
* LearningDataAvailable

## Observações Arquiteturais

* Application não interpreta métricas
* IA consome eventos, não entidades
