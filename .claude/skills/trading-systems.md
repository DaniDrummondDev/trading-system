# Skill — Trading Systems (Strategy & Risk)

## Papel
Atuar como **especialista em sistemas de trading algorítmico**, com foco em:
- Estratégia quantitativa
- Gestão de risco profissional
- Disciplina operacional
- Separação clara entre decisão, execução e controle

Esta skill governa **todas as decisões relacionadas a trading**, independentemente de linguagem, framework ou infraestrutura.

---

## Princípios Não Negociáveis

- Sobrevivência > Lucro
- Risco é controlado antes da entrada
- Estratégia sem risco definido é inválida
- Execução nunca decide estratégia
- Emoção não participa do sistema

---

## Arquitetura Conceitual

O sistema de trading é dividido em **4 camadas lógicas**:

1. Market Data
2. Strategy Engine
3. Risk Engine
4. Execution Engine

Nenhuma camada pode assumir responsabilidades da outra.

---

## Market Data

Responsabilidades:
- Coleta de preços
- Normalização de dados
- Timeframes
- Indicadores técnicos (inputs, não decisões)

Regras:
- Dados são imutáveis após ingestão
- Nunca recalcular histórico com viés futuro
- Latência deve ser conhecida e monitorada

---

## Strategy Engine

Responsabilidades:
- Gerar sinais (BUY / SELL / HOLD)
- Trabalhar com probabilidades, não certezas
- Ser determinística para o mesmo input

Regras:
- Estratégia **não executa ordens**
- Estratégia **não conhece saldo**
- Estratégia **não conhece risco máximo**
- Estratégia apenas propõe oportunidades

Exemplo de outputs válidos:
- Direção
- Confiança
- Setup detectado
- Contexto de mercado

---

## Risk Engine (Camada Crítica)

Responsabilidades:
- Definir se uma operação é permitida
- Calcular:
  - Tamanho da posição
  - Stop loss
  - Take profit
  - Risco por trade
- Bloquear o sistema quando necessário

Regras de Ouro:
- Risco fixo por trade (ex: 0.5%–2%)
- Máximo de risco diário
- Máximo de risco por ativo
- Máximo de operações simultâneas

Se o Risk Engine rejeitar:
➡️ **A operação não existe**

---

## Gestão de Capital

Princípios:
- Capital é um recurso escasso
- Drawdown máximo é definido no início
- Recuperação é mais importante que crescimento

Regras comuns:
- Stop trading após X perdas consecutivas
- Redução automática de size após drawdown
- Proibição de martingale
- Proibição de média contra tendência sem regra explícita

---

## Execution Engine

Responsabilidades:
- Enviar ordens ao broker/exchange
- Gerenciar ordens abertas
- Lidar com:
  - Slippage
  - Partial fills
  - Requotes
  - Falhas de conexão

Regras:
- Execution **não altera estratégia**
- Execution **não ignora risco**
- Toda execução deve ser auditável

---

## Stops & Exits

Todo trade deve existir com:
- Stop técnico ou estatístico
- Take profit definido ou trailing
- Condição clara de invalidação do setup

Regras:
- Stop nunca é removido
- Stop só pode ser ajustado a favor
- Stop é definido antes da entrada

---

## Backtesting

Requisitos:
- Separação entre:
  - In-sample
  - Out-of-sample
- Simular:
  - Slippage
  - Custos
  - Latência

Métricas mínimas:
- Expectancy
- Max Drawdown
- Sharpe / Sortino
- Win rate vs payoff

Backtest que não quebra não é confiável.

---

## Live Trading

Regras:
- Monitoramento em tempo real
- Logs de decisão e execução
- Kill switch manual e automático
- Comparação contínua:
  - Backtest vs Real

Divergência excessiva = parar o sistema.

---

## IA em Trading

Uso permitido:
- Detecção de padrões
- Classificação de contexto
- Otimização de parâmetros
- Análise pós-trade

Uso proibido:
- Tomar decisão final sem Risk Engine
- Ajustar risco em tempo real sem regra
- Operar sem explicabilidade mínima

IA é **assistente**, nunca decisora final.

---

## Logs & Auditoria

Obrigatório registrar:
- Input de mercado
- Sinal gerado
- Decisão do risco
- Execução
- Resultado

Todo trade deve ser reconstruível.

---

## Anti-Padrões Graves

- Martingale
- Aumentar risco após perda
- Estratégia sem stop
- Ajustar regra após prejuízo
- Overfitting
- Operar sem métricas

---

## Critério de Qualidade

Um sistema de trading é considerado profissional se:
- Pode ficar desligado sem quebrar nada
- Sobrevive a longos períodos de drawdown
- Pode ser auditado trade a trade
- Separa claramente decisão, risco e execução
- Respeita RULES.md e ARCHITECTURE.md
