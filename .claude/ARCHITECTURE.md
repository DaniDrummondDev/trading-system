# ARCHITECTURE.md — Visão Macro e Regras de Arquitetura

## Objetivo deste Documento
Este documento define a **arquitetura oficial do sistema**, suas camadas, dependências permitidas e padrões obrigatórios.

Ele existe para garantir que o projeto:
- Escale tecnicamente
- Seja auditável
- Seja seguro
- Não degringole arquiteturalmente com o tempo

Em caso de dúvida técnica, este arquivo deve ser consultado **antes de escrever código**.

---

## Visão Macro da Arquitetura

Arquitetura baseada em:

- Clean Architecture
- DDD Estratégico
- CQRS
- Event-Driven
- IA como consumidor de eventos
- Laravel como framework de entrega

```
┌───────────────────────────┐
│ Interfaces │ ← Controllers, CLI, API
└────────────▲──────────────┘
│
┌────────────┴──────────────┐
│ Application │ ← Use Cases, Commands, Queries
└────────────▲──────────────┘
│
┌────────────┴──────────────┐
│ Domain │ ← Regras de Negócio Puras
└────────────▲──────────────┘
│
┌────────────┴──────────────┐
│ Infrastructure │ ← DB, APIs, IA, Event Bus
└───────────────────────────┘
```

**Dependência sempre aponta para dentro.**

---

## Camadas e Responsabilidades

### 1. Domain
Responsável por:
- Regras de negócio
- Entidades
- Value Objects
- Aggregates
- Eventos de domínio

Proibido no Domain:
- Frameworks
- Banco de dados
- IA
- HTTP
- Jobs
- Filas

---

### 2. Application
Responsável por:
- Casos de uso
- Orquestração
- CQRS (Commands / Queries)
- Coordenação entre Domain e Infrastructure
- Publicação de eventos

Application:
- Depende de Domain
- Define contratos (interfaces)
- Não implementa detalhes técnicos

---

### 3. Infrastructure
Responsável por:
- Persistência (PostgreSQL 18)
- Implementação de Repositories
- Integração com APIs externas (B3, dados)
- Event Bus
- IA (embeddings, vetores, learning pipeline)

Infrastructure:
- Implementa contratos da Application
- Pode depender de Laravel, DB, SDKs

---

### 4. Interfaces
Responsável por:
- HTTP Controllers
- Requests / Validation
- CLI Commands

Interfaces:
- Chamam apenas Application
- Não contêm lógica de negócio

---

## DDD — Bounded Contexts

### Contextos Principais
- Trade
- Journal
- Metrics
- AI Learning (separado)

Comunicação entre contextos:
- Apenas via eventos
- Nunca acesso direto a entidades

---

## CQRS

### Commands
- Alteram estado
- Sempre validados
- Produzem eventos

### Queries
- Apenas leitura
- Sem efeitos colaterais
- Podem usar modelos otimizados

---

## Event-Driven

- Eventos de domínio são imutáveis
- Eventos representam fatos ocorridos
- IA consome eventos, não entidades
- Event Bus abstrato (sync → async)

---

## IA na Arquitetura

A IA:
- Não está no Domain
- Não está na Application
- Vive na Infrastructure

Funções da IA:
- Análise pós-trade
- Extração de features
- Similaridade via vetores
- Feedback ao trader

Limites:
- IA não executa trades
- IA não chama casos de uso

---

## Persistência de Dados

- PostgreSQL 18 como banco principal
- pgvector para embeddings
- Dados operacionais ≠ analíticos
- Histórico nunca é sobrescrito

---

## Segurança e Compliance (Arquitetural)

- Logs imutáveis
- Auditoria por evento
- Separação simulação / real
- Multi-tenant desde o design
- Controle de acesso em todas as camadas de entrada

---

## Testabilidade

- Domain 100% testável sem Laravel
- Application testada via casos de uso
- Infrastructure testada isoladamente
- Nenhum teste depende de IA externa

---

## Anti-Padrões Proibidos

- Anemic Domain Model
- Services gigantes
- Lógica em Controller
- Active Record no Domain
- IA decidindo trade
- Regras “temporárias”

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

Se algo violar este documento, **não deve ser implementado**.
