# UC-02 – Acompanhamento Pós-Trade (Trade Journal & Learning Loop)

## Objetivo
Registrar, estruturar e transformar cada trade executado em **aprendizado mensurável**, fechando o ciclo de decisão → execução → resultado → melhoria do operador.

Este caso de uso é **obrigatório** para evolução do usuário e diferencia o sistema de um simples tracker de trades.

---

## Atores
- Usuário (Trader)
- Sistema

---

## Pré-condições
- Existe uma **TradeDecision** no estado APPROVED
- O trade foi executado externamente (manual ou broker)

---

## Pós-condições
- Trade registrado no Journal
- Resultado consolidado
- Lições obrigatórias armazenadas
- Dados disponíveis para análise histórica e IA

---

## Fluxo Principal

### 1. Registrar Execução do Trade
O usuário informa:
- Preço real de entrada
- Preço de saída
- Quantidade
- Datas (entrada e saída)

O sistema:
- Valida consistência com a decisão original
- Cria um **TradeRecord**

---

### 2. Calcular Resultado
O sistema calcula:
- Resultado financeiro (R$)
- Resultado percentual
- Risco x Retorno real

---

### 3. Classificar Resultado
O sistema classifica o trade como:
- Gain
- Loss
- Breakeven

---

### 4. Registrar Racional e Emoção
O usuário é **obrigado** a informar:
- Seguiu o plano? (sim/não)
- Motivo do desvio (se houver)
- Estado emocional (controlado, ansioso, impulsivo)

---

### 5. Registrar Lição Aprendida
Campo obrigatório:
- O que manter?
- O que corrigir?

O trade **não pode ser fechado** sem esta etapa.

---

### 6. Fechar Trade
O sistema:
- Marca o trade como CLOSED
- Torna o registro imutável

---

## Fluxos Alternativos

### A1 – Trade Não Executado
- A decisão APPROVED expira
- Registro criado como EXPIRED_DECISION

---

### A2 – Execução Divergente do Plano
- Entrada/saída fora do sugerido
- Sistema marca como PLAN_DEVIATION

---

## Regras de Negócio Importantes
- Não é permitido editar trades fechados
- Não é permitido remover trades
- Toda perda exige lição registrada

---

## Dados Gerados
- Estatísticas por ativo
- Estatísticas por setup
- Estatísticas comportamentais

---

## Papel da IA (Observador)
- Detectar padrões de erro
- Correlacionar emoção x resultado
- Sugerir ajustes de processo

A IA **não altera dados**, apenas interpreta.

---

## Resultado Final
Cada trade gera:
- Histórico confiável
- Aprendizado explícito
- Base real para evolução técnica e psicológica

---

## Observação Crítica
Sem Journal, não existe trader consistente. Este caso de uso **não é opcional** no sistema.

