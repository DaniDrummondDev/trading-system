# IA – Learning Loop (Retroalimentação Decisão → Métrica → Ajuste)

## Objetivo
Definir como a IA atua **exclusivamente como sistema de aprendizado e feedback**, utilizando dados reais do Trade Journal e KPIs para **melhorar a qualidade das decisões futuras**, sem automatizar trades.

---

## Princípios Inegociáveis
- IA **não executa** trades
- IA **não aprova** trades
- IA **não altera** dados históricos
- IA atua como **observador analítico**

---

## Visão Geral do Loop

1. Decisão de Trade (UC-01)
2. Execução Externa
3. Registro no Journal (UC-02)
4. Cálculo de KPIs
5. Análise pela IA
6. Geração de Feedback
7. Ajuste de Parâmetros Humanos
8. Nova Decisão (UC-01)

O loop é **lento, incremental e cumulativo** — propositalmente.

---

## Entradas da IA

### Dados Estruturados
- TradeDecision (histórico)
- TradeRecord (Journal)
- KPIs técnicos
- KPIs comportamentais

### Contexto Operacional
- Timeframe
- Setup (Tendência + Pullback)
- Mercado (B3)

---

## Processos Internos da IA

### 1. Pattern Detection
- Correlação setup × resultado
- Correlação emoção × prejuízo
- Identificação de erros recorrentes

---

### 2. Bias Detection
- Overconfidence
- Revenge trading
- Overtrading

---

### 3. Consistency Analysis
- Sequência de trades fora do plano
- Degradação de performance após perdas

---

## Saídas da IA (Somente Sugestões)

### Feedback Operacional
- "Você perde mais quando ignora o stop"
- "Trades em pullback raso têm menor expectancy"

---

### Alertas Preventivos
- "Risco comportamental elevado hoje"
- "Sequência fora do plano detectada"

---

### Recomendações de Processo
- Reduzir número de trades
- Aumentar filtro de tendência
- Pausar após X perdas

---

## Integração com UC-01

Antes de uma nova análise:
- A IA fornece **contexto adicional**, não decisão
- Exemplo:
  - "Setup válido, mas histórico recente do usuário indica baixa disciplina"

---

## Persistência e Evolução

### Learning Snapshot
- Período analisado
- Padrões detectados
- Recomendações emitidas

Snapshots são versionados e auditáveis.

---

## Métricas de Qualidade da IA
- Taxa de recomendações seguidas
- Impacto positivo após ajustes
- Redução de drawdown comportamental

---

## Observação Crítica Final
A IA não cria lucro.
Ela **reduz erros repetidos**.
O aprendizado real continua sendo humano.
