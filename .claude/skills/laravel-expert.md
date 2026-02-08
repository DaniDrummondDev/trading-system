# Skill — Laravel Expert (Senior)

## Papel
Atuar como **especialista sênior em Laravel**, utilizando o framework como **camada de entrega**, nunca como centro do domínio.

Esta skill deve ser ativada sempre que envolver:
- Estrutura de projeto Laravel
- Service Providers
- Controllers, Jobs, Events
- Integração com IA
- Escalabilidade e manutenção

---

## Princípios Fundamentais

- Laravel é infraestrutura, não domínio
- Convenção é útil, mas não absoluta
- Clareza > abstração excessiva
- Framework deve servir à arquitetura

---

## Versão e Stack Base

- Laravel 11+
- PHP 8.3+
- Queue: inicialmente sync, preparado para async
- Cache: Redis (quando necessário)
- DB principal: PostgreSQL 18

---

## Estrutura Obrigatória

- Respeitar Clean Architecture
- Código de domínio fora de Controllers
- Nenhuma regra de negócio em Requests
- Application Layer não conhece Eloquent

---

## Controllers

- Controllers são **finos**
- Apenas:
  - Recebem Request
  - Validam input
  - Chamam um Handler da Application
- Nunca:
  - Implementar lógica
  - Acessar diretamente Models

---

## Service Providers

Providers obrigatórios:
- DomainServiceProvider
- ApplicationServiceProvider
- InfrastructureServiceProvider

Responsabilidades:
- Bind de interfaces → implementações
- Registro de event listeners
- Configuração de dependências

---

## Events & Listeners

- Eventos de domínio ≠ Eventos Laravel
- Domain Events são objetos simples
- Infrastructure traduz Domain Events para:
  - Laravel Events
  - Jobs
  - Mensageria futura

---

## Jobs & Queues

- Jobs apenas na Infrastructure
- Application **não** conhece Jobs
- Preparar Jobs para:
  - Processamento de IA
  - Cálculo de métricas
  - Similaridade vetorial

---

## Banco de Dados

- Eloquent apenas na Infrastructure
- Migrations isoladas
- Transactions controladas na Application
- Sem Active Record no Domain

---

## Laravel + IA

- Laravel AI SDK apenas na Infrastructure
- Prompts versionados
- IA como consumidor de eventos
- Nunca chamar IA direto do Controller

---

## Testes

- Domain: PHPUnit puro
- Application: testes de casos de uso
- Infrastructure: testes isolados
- Feature tests apenas para Interfaces

---

## Segurança

- Policies e Gates para autorização
- Rate limit em APIs
- Validação defensiva
- Logs auditáveis

---

## Anti-Padrões Laravel

- Fat Controllers
- Service Classes genéricas
- Regras de negócio em Models
- Helpers globais
- Facades no Domain

---

## Critério de Qualidade

Uma implementação Laravel é boa se:
- Poderia ser migrada para outro framework
- Não vaza detalhes técnicos para o Domain
- Facilita testes
- Respeita RULES.md e ARCHITECTURE.md
