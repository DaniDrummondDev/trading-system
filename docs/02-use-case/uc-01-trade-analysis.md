# UC-01 – Análise e Decisão de Trade (Tendência + Pullback)

## Objetivo do Caso de Uso
Permitir que o usuário (trader) identifique oportunidades de trade no mercado brasileiro (B3), em **timeframe diário**, utilizando a estratégia de **tendência + pullback**, com apoio de análise automatizada e IA, mantendo a **decisão final sempre humana**.

---

## Atores
- **Trader (Usuário)** – responsável pela decisão final e execução do trade
- **Sistema de Análise** – orquestra dados, regras e IA
- **Provedor de Dados de Mercado** – fornece candles, volume e indicadores brutos
- **Módulo de IA** – gera análises probabilísticas e cenários

---

## Pré-condições
- Usuário autenticado
- Mercado selecionado: **Brasil (B3)**
- Estratégia ativa: **Tendência + Pullback**
- Timeframe configurado: **Diário (D1)**
- Dados de mercado disponíveis e atualizados

---

## Gatilho
O usuário solicita uma **análise de oportunidades de trade** para o pregão atual ou próximo pregão.

---

## Fluxo Principal (Happy Path)

### 1. Seleção do Universo de Ativos
- Usuário seleciona:
  - Lista fixa (ex: IBOV, SMLL, watchlist pessoal)
  - ou filtro automático (liquidez mínima, volatilidade, preço)

### 2. Coleta de Dados de Mercado
- Sistema requisita ao **Provedor de Dados**:
  - Candles diários (OHLCV)
  - Histórico mínimo (ex: 200 períodos)
- Dados são normalizados e armazenados

### 3. Identificação de Tendência
- Motor de regras avalia:
  - Estrutura de mercado (topos e fundos)
  - Médias móveis (ex: 20 / 50 / 200)
  - Direção predominante (alta, baixa ou lateral)
- Ativos sem tendência clara são descartados

### 4. Detecção de Pullback
- Para ativos em tendência válida:
  - Sistema verifica retrações até zonas de valor
  - Exemplo:
    - Pullback até média
    - Pullback até suporte/resistência
    - Correção percentual aceitável

### 5. Validação Técnica
- Regras adicionais são aplicadas:
  - Volume compatível
  - Candle de sinal (ex: rejeição, engolfo)
  - Relação risco x retorno mínima
- Ativos inválidos são eliminados

### 6. Geração de Cenários pela IA
- Para cada ativo elegível, a IA produz:
  - Cenário otimista
  - Cenário neutro
  - Cenário adverso
- A IA **não executa trades**, apenas:
  - Estima probabilidades
  - Explica riscos
  - Destaca pontos de atenção

### 7. Consolidação da Análise
- Sistema gera um **Relatório de Trade**, contendo:
  - Ativo
  - Tendência identificada
  - Região de entrada sugerida
  - Stop técnico
  - Alvos possíveis
  - Probabilidades estimadas
  - Justificativa técnica e estatística

### 8. Apresentação ao Usuário
- Usuário visualiza:
  - Ranking de oportunidades
  - Detalhamento gráfico
  - Texto explicativo (sem viés emocional)

### 9. Decisão Humana
- Usuário escolhe:
  - Ignorar
  - Marcar para acompanhamento
  - Executar o trade externamente (corretora)

---

## Pós-condições
- Trade pode ser registrado como:
  - Executado
  - Não executado
- Dados ficam disponíveis para:
  - Backtesting
  - Aprendizado futuro
  - Avaliação de performance

---

## Fluxos Alternativos

### A1 – Dados Indisponíveis
- Sistema informa falha
- Sugere novo horário ou outro ativo

### A2 – Nenhuma Oportunidade Encontrada
- Sistema retorna resultado vazio
- Reforça disciplina: **"Não operar também é uma decisão"**

---

## Regras de Negócio Críticas
- O sistema **não envia ordens ao mercado**
- IA não decide, apenas **assessora**
- Estratégia é explícita, auditável e reproduzível
- Qualquer automação futura exige consentimento explícito

---

## Observações Estratégicas (Realistas)
- Esse fluxo **não garante lucro**
- O valor do sistema está em:
  - Redução de ruído
  - Consistência
  - Disciplina operacional
- Performance depende mais do usuário do que da IA

---

## Evoluções Naturais Futuras
- Outros timeframes (H4, H1)
- Mercado americano (US)
- Execução automatizada (opcional)
- Aprendizado baseado no histórico do próprio usuário

