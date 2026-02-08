# RULES.md — Regras Não Negociáveis do Projeto

## Princípio Central
Este projeto deve ser desenvolvido **como se fosse auditado por uma corretora, regulador ou auditor externo**.  
Segurança, conformidade, rastreabilidade e clareza **não são opcionais**.

Estas regras **não podem ser violadas**, mesmo em protótipos, testes ou MVP.

---

## 1. Arquitetura (Obrigatório)

- Clean Architecture é mandatória
- DDD estratégico com Bounded Contexts explícitos
- CQRS nos casos de uso principais
- Domain **nunca** depende de:
  - Framework (Laravel)
  - Infraestrutura
  - IA
  - Banco de dados
- Application orquestra, Domain decide, Infrastructure executa
- Nenhum atalho arquitetural “temporário” é permitido

---

## 2. Segurança (Obrigatória e Inegociável)

### 2.1 Segurança de Software
- OWASP Top 10 (Web + API) deve ser seguido
- Validação de entrada em **todas** as bordas do sistema
- Nenhuma credencial, token ou chave em código
- Secrets apenas via `.env`, Vault ou serviço equivalente
- Logs nunca expõem dados sensíveis
- Princípio do menor privilégio (Zero Trust)

### 2.2 Segurança Financeira
- IA **nunca** executa ordens reais
- Nenhuma automação de trade sem confirmação explícita do trader
- Separação clara entre:
  - Simulação
  - Paper trade
  - Operação real
- Todas as decisões devem ser auditáveis
- Capital do usuário é tratado como **ativo crítico**

### 2.3 Segurança Nacional e Internacional
- LGPD (Brasil) obrigatória desde o design
- GDPR (União Europeia) considerada desde o início
- Princípios de SOC2:
  - Controle de acesso
  - Auditoria
  - Rastreabilidade
- Criptografia:
  - Em trânsito (TLS)
  - Em repouso (dados sensíveis)

---

## 3. Governança de Risco (Obrigatória)

- Preservação de capital é prioridade absoluta
- Risco máximo por trade deve ser definido e aplicado
- Risco máximo diário deve ser definido e aplicado
- Drawdown máximo global deve ser definido e aplicado
- Exposição agregada (multi-mercado) deve ser monitorada

### Kill Switch
- Kill switch manual e automático é obrigatório
- Violação de regras de risco ⇒ bloqueio imediato de novas operações
- Kill switch deve ser auditável e reversível apenas com justificativa

---

## 4. Compliance de Mercado

- Nenhuma promessa de retorno financeiro
- Nenhum “conselho financeiro automático”
- IA atua apenas como **apoio à decisão**
- Logs imutáveis de:
  - Decisões
  - Entradas
  - Saídas
  - Ajustes manuais
- Histórico **nunca** pode ser reescrito silenciosamente
- Sistema preparado para auditoria (CVM / SEC-like)

---

## 5. Inteligência Artificial — Limites Claros

- IA **não é trader**
- IA **não toma decisões finais**
- IA **não executa ordens**
- IA **não ajusta risco ou tamanho de posição**
- IA **não aprende online em produção**
- Todo output da IA deve ser:
  - Explicável
  - Versionado
  - Rastreável
- Prompts são tratados como código (versionados)

---

## 6. Dados

- Dados históricos são versionados
- Dados brutos são imutáveis
- Métricas não podem ser recalculadas sem registro
- PostgreSQL 18 como banco principal
- pgvector como padrão para dados vetoriais
- Separação clara entre:
  - Dados operacionais
  - Dados analíticos
  - Dados de aprendizado

---

## 7. Qualidade de Código

- Código legível > código “esperto”
- Testes obrigatórios para:
  - Casos de uso
  - Regras críticas
- Nenhum atalho arquitetural “temporário”
- Overengineering também é erro

---

## 8. Evolução para SaaS

- Multi-tenant considerado desde o início
- Isolamento de dados por usuário (tenant)
- Controle rigoroso de permissões
- Auditoria por tenant
- Segurança cresce junto com features

---

## Regra de Precedência

Em caso de conflito:

RULES.md  
⬇  
ARCHITECTURE.md  
⬇  
CONTEXT.md  
⬇  
Skills  

Se algo violar este arquivo, **não deve ser implementado**.

**Todas as respostas, códigos e documentos devem ser em Português.**
