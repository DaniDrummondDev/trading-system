# CONTEXT.md — Contexto Permanente do Projeto

## Visão Geral
Este projeto é um **sistema de apoio à decisão para trading**, criado inicialmente para uso pessoal e aprendizado, com evolução planejada para um **SaaS profissional**, respeitando rigorosamente segurança, compliance e boas práticas do mercado financeiro.

O sistema **não é uma corretora**, **não executa ordens automaticamente** e **não fornece aconselhamento financeiro**.

---

## Objetivo Principal
Ajudar o trader a:
- Analisar o mercado de forma estruturada
- Tomar decisões mais consistentes
- Aprender com seus próprios trades
- Reduzir vieses comportamentais
- Evoluir tecnicamente ao longo do tempo

---

## Perfil do Usuário
- Trader pessoa física
- Perfil técnico (programador)
- Busca consistência no **curto e médio prazo**
- Foco inicial em aprendizado + preservação de capital
- Evolução gradual para sofisticação

---

## Mercado e Escopo Inicial

### Mercado
- **Inicial:** Brasil (B3)
- **Futuro:** Estados Unidos (NYSE / NASDAQ)

### Ativos (inicial)
- Ações
- ETFs
- (Derivativos apenas em fases futuras)

### Timeframe
- **Inicial:** Diário
- **Futuro:** Configurável (semanal, intraday, etc.)

---

## Estratégia Base
- **Tendência + Pullback**
- Gestão de risco obrigatória
- Risco por trade limitado
- Expectância positiva como métrica central
- Simplicidade > complexidade

---

## Papel da IA no Sistema
- Apoio à decisão (não decisor)
- Análise de contexto
- Identificação de padrões históricos
- Feedback pós-trade
- Aprendizado baseado em métricas reais

A IA **nunca**:
- Executa trades
- Substitui o trader
- Promete resultados

---

## Casos de Uso Principais

### UC-01 — Execução de Trade
- Análise de mercado
- Validação de setup
- Cálculo de risco
- Registro da decisão

### UC-02 — Acompanhamento Pós-Trade
- Trade Journal
- Métricas de performance
- Métricas comportamentais
- Learning Loop

---

## Arquitetura (Visão de Contexto)
- Clean Architecture
- DDD estratégico
- CQRS
- Event-driven
- Laravel como framework de entrega
- PostgreSQL 18 + pgvector

---

## Restrições Importantes
- Segurança e compliance são prioritários
- Auditoria completa de decisões
- Separação clara entre simulação e real
- Evolução para SaaS sem refatorações traumáticas

---

## Expectativa sobre o Claude
Ao atuar neste projeto, o Claude deve assumir o papel de:
- **Engenheiro / Arquiteto de Software Sênior**
- Conhecimento profundo em:
  - Mercado financeiro
  - Trading sistemático
  - Laravel
  - IA aplicada
  - Bancos de dados vetoriais
- Postura crítica, realista e técnica
- Nunca sugerir atalhos que violem RULES.md

---

## Regra Final
Este arquivo existe para **evitar perda de contexto ao longo do tempo**.

Se houver dúvida sobre intenção, escopo ou direção:
➡️ **este documento deve ser consultado primeiro**.
