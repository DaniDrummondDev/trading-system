# Skill — Market Data & Indicators

## Papel
Definir padrões de **coleta, normalização, versionamento e uso**
de dados de mercado.

Dados são a base de tudo. Dados ruins quebram qualquer sistema.

---

## Princípios Não Negociáveis

- Dados não mentem, mas podem enganar
- Sem lookahead bias
- Sem recalcular histórico
- Sem misturar timeframes indevidamente

---

## Tipos de Dados

- Preço (OHLCV)
- Order book (se aplicável)
- Indicadores técnicos
- Eventos de mercado
- Meta-dados (latência, origem)

---

## Ingestão de Dados

Regras:
- Timestamp confiável
- Fonte identificável
- Dados brutos preservados
- Normalização posterior

Nunca alterar dados brutos.

---

## Timeframes

- Timeframe é uma decisão estratégica
- Indicadores nunca misturam timeframes sem regra explícita
- Agregações devem ser determinísticas

---

## Indicadores Técnicos

Indicadores:
- São inputs
- Não são sinais
- Não decidem trades

Exemplos permitidos:
- Médias
- Volatilidade
- Momentum
- Volume

---

## Qualidade dos Dados

Monitorar:
- Buracos
- Outliers
- Latência
- Divergência entre fontes

Dados ruins devem ser descartados ou sinalizados.

---

## Uso em Backtest

- Dados congelados por versão
- Mesmo dataset do treino
- Simulação de custos e latência

Backtest sem dados realistas é inválido.

---

## Uso em Produção

- Cache controlado
- Fallback de fontes
- Alertas de inconsistência

---

## Logs & Auditoria

Registrar:
- Fonte
- Timestamp
- Ajustes
- Versão

---

## Anti-Padrões Graves

- Recalcular histórico
- Ajustar dado para “funcionar”
- Indicador como decisão final
- Misturar feeds sem validação

---

## Critério de Qualidade

Dados são confiáveis se:
- Podem ser reproduzidos
- São rastreáveis
- Não mudam com o tempo
- Sustentam auditoria
