# Trading App with AI -- Concept and Strategy Definition

## Objectives

-   Operate in the stock market (B3) for short and medium term
-   Use the system primarily for personal learning and trading
-   Evolve later into a SaaS for similar users

## Strategic Principles

1.  Realistic expectations
2.  Critical thinking about trading and AI
3.  True, non‑romanticized system behavior

## Fundamental Truths

-   Short-term trading in B3 is difficult
-   IA does not predict the market consistently
-   The system must enforce discipline, not promise profits

## Product Philosophy

A disciplined decision copilot, not an automatic trader.

### What the app does

-   Centralizes data
-   Forces process
-   Questions bad decisions
-   Builds reliable history

### What the app does not do

-   Promise profits
-   Auto-execute trades
-   "Discover" magic opportunities

## MVP Scope

### Market

-   Brazil (B3)
-   IBOV + liquid stocks

### Strategy

Trend + Pullback

### Timeframe

Daily candles only (D1)

### Core Features

1.  Simple fundamental filter
2.  Single technical setup
3.  Mandatory risk management
4.  Structured trading journal

## Trend + Pullback Setup Definition

### Trend Rules

-   Price above EMA 21 and EMA 50
-   EMA 21 above EMA 50

### Pullback Rules

-   Retracement to EMA 21 or 50
-   RSI between 40 and 55
-   Lower volume than impulse leg

### Entry Trigger

One defined rule (e.g., candle strength or breakout).

### Risk Rules

-   Stop at pullback low
-   Fixed risk per trade
-   Minimum R:R requirement

## System Flow

1.  Fundamental filter
2.  Technical setup validation
3.  Risk evaluation
4.  Execution
5.  Post‑trade review

## Architectural Principles

-   DDD + Clean Architecture
-   Data providers abstracted via interfaces
-   Timeframe treated as data, not code
-   No realtime or auto trading in MVP
