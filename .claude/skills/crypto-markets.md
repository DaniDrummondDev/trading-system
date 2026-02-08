# Skill — Crypto Markets (Spot & Derivatives)

## Papel
Definir regras, riscos e particularidades do **mercado de criptoativos**,
incluindo spot e derivativos, respeitando a arquitetura global do sistema.

---

## Princípios Fundamentais

- Cripto ≠ mercado tradicional
- Volatilidade é estrutural
- Liquidez varia drasticamente
- Infraestrutura é parte do risco

---

## Características do Mercado

- Operação 24/7
- Alta fragmentação de liquidez
- Forte impacto de eventos externos
- Alta correlação entre ativos

---

## Tipos de Operação Permitidos

- Spot
- Perpétuos (futuros sem vencimento)
- Futures (quando aplicável)

---

## Restrições Obrigatórias

- Alavancagem limitada por perfil
- Risco por trade menor que mercados tradicionais
- Stop sempre obrigatório
- Proibição de martingale

---

## Riscos Específicos

- Slippage extremo
- Wicks artificiais
- Falhas de exchange
- Congestionamento de rede
- Delistings inesperados

---

## Dados de Mercado

- Preferência por múltiplas fontes
- Normalização obrigatória
- Verificação de latência
- Divergência entre exchanges deve ser tratada

---

## Gestão de Risco

- Risco diário conservador
- Limite de exposição por ativo
- Limite por exchange
- Kill switch por volatilidade extrema

---

## Compliance & Custódia

- Não assumir custódia de ativos
- Chaves nunca armazenadas
- Uso de APIs com permissões mínimas
- Preparação para exigências regulatórias futuras

---

## IA no Mercado Cripto

Permitido:
- Detecção de regimes
- Classificação de volatilidade
- Análise pós-trade

Proibido:
- Ajuste dinâmico de alavancagem
- Execução automática baseada em IA

---

## Anti-Padrões Graves

- Overtrading por “oportunidade infinita”
- Ignorar funding rates
- Confiar em uma única exchange
- Estratégias copiadas sem validação

---

## Critério de Qualidade

A atuação em cripto é correta se:
- Sobrevive a eventos extremos
- Mantém risco controlado
- Não depende de exchange única
- Pode ser desligada instantaneamente
