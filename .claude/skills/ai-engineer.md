# Skill — AI Engineer (Learning & Decision Support)

## Papel
Atuar como **engenheiro de IA aplicada a sistemas críticos**, garantindo que modelos:
- Aprendam de forma controlada
- Nunca violem regras de risco
- Sejam auditáveis, explicáveis e reversíveis

A IA **não é trader**, **não é risk manager** e **não executa ordens**.

---

## Princípios Não Negociáveis

- IA não decide sozinha
- IA não ajusta risco em tempo real
- IA não aprende sem validação
- IA nunca substitui regras explícitas
- IA é sempre supervisionada

---

## Posição da IA na Arquitetura

A IA atua apenas em **3 pontos**:

1. Análise de contexto de mercado
2. Classificação pós-trade (Trade Journal)
3. Otimização offline de parâmetros

Nunca no loop direto de execução.

---

## Ciclo de Aprendizado (Learning Loop)

1. Coleta de dados (Market + Trades)
2. Extração de features
3. Treinamento offline
4. Validação estatística
5. Aprovação humana ou automática
6. Deploy controlado
7. Monitoramento
8. Possível rollback

Sem exceções.

---

## Tipos de Modelos Permitidos

- Classificadores (regimes de mercado)
- Regressões (expectativa, volatilidade)
- Clustering (padrões de comportamento)
- NLP (journaling e anotações)

Modelos proibidos:
- Black-box sem explicabilidade mínima
- Modelos auto-adaptativos online
- Modelos que ajustam capital diretamente

---

## Dados de Treinamento

Regras:
- Dados versionados
- Separação temporal (no leakage)
- Amostras balanceadas
- Dataset congelado por versão

Cada modelo conhece:
- Dataset
- Período
- Métricas
- Limitações

---

## Validação Obrigatória

Antes de qualquer uso:
- Teste fora da amostra
- Stress test
- Análise de estabilidade
- Comparação com baseline simples

Modelo pior que baseline é descartado.

---

## Integração com o Sistema

A IA **só pode produzir recomendações**, por exemplo:
- Classificação de regime
- Score de qualidade do trade
- Sugestão de ajuste de parâmetro

Sempre consumidas por:
- Strategy Engine
- Risk Engine
- Learning Loop

Nunca direto para Execution.

---

## Monitoramento de Drift

Obrigatório monitorar:
- Drift de dados
- Drift de performance
- Drift comportamental

Drift excessivo:
➡️ desativa o modelo automaticamente

---

## Logs & Auditoria

Cada inferência deve registrar:
- Input
- Output
- Modelo e versão
- Timestamp
- Contexto

IA sem log = IA inválida.

---

## Anti-Padrões Graves

- Auto-trading por IA
- Aprendizado online em produção
- Ajustar risco baseado em emoção detectada
- “Modelo mágico”
- Fine-tuning sem controle

---

## Critério de Qualidade

A IA é considerada correta se:
- Pode ser desligada sem impacto crítico
- Nunca viola RULES.md
- Nunca ignora Risk Engine
- Pode ser auditada e revertida
