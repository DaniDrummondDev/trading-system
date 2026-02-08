# Bounded Contexts -- Trading Domain

## Overview

The system is divided into independent bounded contexts with clear
responsibilities and language.

------------------------------------------------------------------------

## 1. Market Data Context

### Mission

Represent normalized market data.

### Concepts

-   Asset (Entity)
-   Candle (Value Object)
-   Timeframe (Value Object)
-   MarketSeries (Aggregate Root)

### Rules

-   No strategy
-   No user data
-   No decisions

------------------------------------------------------------------------

## 2. Analysis Context

### Mission

Transform market data into trading information.

### Concepts

-   Indicator (Value Object)
-   Trend (Value Object)
-   Pullback (Value Object)
-   SetupAnalysis (Aggregate Root)
-   TrendPullbackStrategy (Domain Service)

### Rules

-   Reads market data
-   No risk or money logic
-   No execution

------------------------------------------------------------------------

## 3. Risk Management Context

### Mission

Protect capital and enforce discipline.

### Concepts

-   RiskProfile (Value Object)
-   TradeRisk (Value Object)
-   PositionSize (Value Object)
-   RiskEvaluation (Aggregate Root)
-   RiskCalculator (Domain Service)

### Rules

-   Can block operations
-   No market analysis
-   No execution

------------------------------------------------------------------------

## 4. Trading Journal Context

### Mission

Record decisions and learning.

### Concepts

-   Trade (Entity)
-   TradeRationale (Value Object)
-   TradeOutcome (Value Object)
-   TradeLesson (Value Object)
-   TradeRecord (Aggregate Root)

### Rules

-   Trade cannot close without lesson
-   History is immutable
-   No recalculation of setup or risk

------------------------------------------------------------------------

## 5. User & Profile Context

### Mission

Provide user parameters and constraints.

### Concepts

-   TradingProfile (Value Object)
-   RiskProfile (Value Object)

### Rules

-   No market logic
-   No trade execution
-   Only provides parameters

------------------------------------------------------------------------

## 6. AI Insights Context

### Mission

Explain patterns and behaviors.

### Concepts

-   Insight
-   Error patterns
-   Historical comparisons

### Rules

-   Read-only
-   No decisions
-   No blocking or execution

------------------------------------------------------------------------

## Context Flow

Market Data → Analysis → Risk Management → Trading Journal

User/Profile supplies parameters. AI Insights observes all contexts.
