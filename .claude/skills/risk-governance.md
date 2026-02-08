# Skill — Risk Governance & Compliance

## Papel
Definir e garantir **governança de risco global**, compliance regulatório
e mecanismos de proteção do sistema, do capital e do usuário.

Esta skill tem **precedência sobre todas as outras**.

---

## Princípios Fundamentais

- Capital é prioridade absoluta
- Nenhuma estratégia está acima do risco
- Falhas devem ser contidas
- Regras são globais e imutáveis em runtime

---

## Tipos de Risco Controlados

- Risco financeiro
- Risco operacional
- Risco tecnológico
- Risco regulatório
- Risco comportamental

---

## Regras Globais de Risco

Exemplos obrigatórios:
- Risco máximo por trade
- Risco máximo diário
- Drawdown máximo permitido
- Máximo de trades simultâneos
- Limite por ativo / mercado

Violação = bloqueio imediato.

---

## Kill Switch

Tipos:
- Manual (usuário/admin)
- Automático (regra)
- Emergencial (infraestrutura)

Kill switch:
- Cancela novas ordens
- Fecha posições se configurado
- Gera alerta e log crítico

---

## Compliance & Regulação

O sistema deve estar preparado para:
- LGPD / GDPR
- KYC / AML (se aplicável)
- Auditoria externa
- Logs imutáveis

Nenhum dado sensível sem criptografia.

---

## Governança de Estratégias

Toda estratégia deve:
- Ter responsável
- Ter versão
- Ter métricas mínimas
- Ter critério de desligamento

Estratégia sem dono = proibida.

---

## Governança de IA

- Modelos aprovados
- Versionados
- Com rollback
- Com métricas mínimas

Modelo fora de controle é desligado.

---

## Monitoramento Contínuo

Monitorar em tempo real:
- Exposição total
- Drawdown
- Latência
- Falhas de execução
- Divergência esperado vs real

---

## Logs & Auditoria

Obrigatório:
- Logs imutáveis
- Trilha completa de decisões
- Assinatura temporal
- Retenção configurável

---

## Anti-Padrões Graves

- Ignorar limites
- Override manual sem log
- Ajustar risco após perda
- Falta de kill switch
- Estratégia “temporária”

---

## Critério de Qualidade

A governança é correta se:
- Consegue parar tudo em segundos
- Protege o sistema de si mesmo
- Sobrevive a falhas parciais
- Respeita leis e regras globais
