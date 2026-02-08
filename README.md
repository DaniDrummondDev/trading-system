# Trading System

Sistema profissional de **análise, aprendizado e apoio à decisão em trading**,
projetado com Clean Architecture, DDD e CQRS.

O sistema atua como um **copiloto disciplinado** — centraliza dados, força processo,
questiona decisões ruins e constrói histórico confiável. Ele **não** promete lucros,
não executa trades automaticamente e não substitui o julgamento humano.

## Stack

| Tecnologia | Versão |
|------------|--------|
| PHP | 8.5 |
| Laravel | 12 |
| PostgreSQL | 18 (pgvector 0.8.1) |
| Redis | Alpine |
| Pest | 4 (PHPUnit 12) |
| Laravel AI SDK | 0.1.3 (prism-php) |
| Docker | Compose V2 |

## Pré-requisitos

- [Docker](https://docs.docker.com/get-docker/) com Docker Compose V2
- Git

## Instalação

```bash
# 1. Clone o repositório
git clone git@github.com:seu-usuario/trading-system.git
cd trading-system

# 2. Execute o script de setup
chmod +x setup.sh
./setup.sh
```

O `setup.sh` executa automaticamente:

1. Build das imagens Docker (PHP 8.5-FPM, nginx, PostgreSQL 18, Redis)
2. Instalação do Laravel 12
3. Configuração do `.env` para os containers
4. Instalação dos pacotes (Laravel AI SDK, Pest 4, pest-plugin-arch)
5. Configuração da API (Sanctum) e inicialização do Pest
6. Criação da estrutura Clean Architecture
7. Criação dos contratos e Service Providers base
8. Start dos serviços, migrations e geração de key

### Instalação manual

Se preferir executar passo a passo:

```bash
# Build e start
docker compose build
docker compose up -d

# Instalar dependências
docker compose exec app composer install

# Configurar ambiente
cp .env.example .env
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
```

## Uso

```bash
# Subir serviços
docker compose up -d

# Parar serviços
docker compose down

# Entrar no container
docker compose exec app bash

# Rodar testes
docker compose exec app ./vendor/bin/pest

# Rodar testes de arquitetura
docker compose exec app ./vendor/bin/pest --group=arch

# Artisan
docker compose exec app php artisan <comando>
```

### Serviços

| Serviço | URL |
|---------|-----|
| API | http://localhost:8000 |
| PostgreSQL | localhost:5432 |
| Redis | localhost:6379 |

## Arquitetura

O projeto segue **Clean Architecture** com **DDD** (Domain-Driven Design) e **CQRS**
(Command Query Responsibility Segregation).

```
app/
├── Domain/              Regras de negócio puras (zero dependência de framework)
│   ├── Trade/           Bounded Context: ciclo de vida do trade
│   ├── Journal/         Bounded Context: registro e aprendizado pós-trade
│   ├── Metrics/         Bounded Context: KPIs e métricas do trader
│   └── Shared/          Shared Kernel (DomainEvent, base classes)
│
├── Application/         Orquestração de use cases via CQRS
│   ├── UC01_TradeExecution/   Análise e decisão de trade
│   ├── UC02_TradeJournal/     Journal e learning loop
│   └── Contracts/             Interfaces para Infrastructure
│
├── Infrastructure/      Implementações técnicas
│   ├── Persistence/     Eloquent models e repositórios
│   ├── MarketData/      Integração com dados de mercado (B3)
│   ├── AI/              IA como observador (prompts, features, learning)
│   └── EventBus/        Publicação de eventos de domínio
│
└── Interfaces/          Pontos de entrada externos
    ├── Http/            Controllers e Requests da API REST
    └── Console/         Comandos CLI
```

### Dependência entre camadas

```
Interfaces → Application → Domain ← Infrastructure
```

A dependência aponta sempre para dentro. O Domain **nunca** depende de framework,
banco de dados ou IA.

### Hierarquia de autoridade

```
1. RULES.md              (autoridade máxima)
2. Governança de Risco
3. ARCHITECTURE.md
4. CONTEXT.md
5. Skills
```

## Escopo

### Foco inicial (MVP)

- **Mercado**: Brasil (B3)
- **Ativos**: Ações e ETFs do IBOV
- **Timeframe**: Diário (D1)
- **Estratégia**: Trend + Pullback
- **Gestão de risco**: Obrigatória em cada operação

### Use Cases

| UC | Descrição |
|----|-----------|
| UC-01 | **Análise e Decisão de Trade** — identifica oportunidades via Trend+Pullback, valida risco, gera decisão (ALLOW/BLOCK/WAIT). Humano sempre decide. |
| UC-02 | **Journal e Learning Loop** — registra execução, resultado, comportamento e lições. Trade não fecha sem lição. Base para IA e métricas. |

### IA no projeto

A IA atua **exclusivamente como observador e sistema de feedback**:

- Detecta padrões de erro recorrentes
- Correlaciona emoção com resultado
- Sugere ajustes de processo
- **Nunca** executa, aprova ou bloqueia trades
- Desligável e reversível a qualquer momento

## Testes

```bash
# Todos os testes
docker compose exec app ./vendor/bin/pest

# Testes unitários
docker compose exec app ./vendor/bin/pest tests/Unit

# Testes de integração
docker compose exec app ./vendor/bin/pest tests/Integration

# Testes de feature (API)
docker compose exec app ./vendor/bin/pest tests/Feature

# Com cobertura
docker compose exec app ./vendor/bin/pest --coverage
```

## Documentação

| Seção | Conteúdo |
|-------|----------|
| [00 - Visão e Escopo](docs/00-vision-and-scope/) | Conceito, estratégia, filosofia do projeto |
| [01 - Domínio](docs/01-domain/) | Bounded Contexts, modelos de domínio, state machines |
| [02 - Use Cases](docs/02-use-case/) | Fluxos detalhados do UC-01 e UC-02 |
| [03 - Application Layer](docs/03-application-layer/) | CQRS, commands, queries, handlers, DTOs |
| [04 - Métricas e Learning](docs/04-metrics-and-learning/) | KPIs, Learning Loop, integração com IA |
| [05 - Arquitetura](docs/05-arquitecture/) | Estrutura de pastas, namespaces, contratos |
| [06 - Roadmap](docs/06-roadmap/) | Fases de implementação e critérios de aceite |

### Contexto do Claude

| Arquivo | Propósito |
|---------|-----------|
| [CLAUDE.md](.claude/CLAUDE.md) | Contexto operacional do Claude no projeto |
| [RULES.md](.claude/RULES.md) | Regras invioláveis (autoridade máxima) |
| [ARCHITECTURE.md](.claude/ARCHITECTURE.md) | Decisões e restrições arquiteturais |
| [CONTEXT.md](.claude/CONTEXT.md) | Contexto de negócio e mercado |
| [Skills](.claude/skills/) | 11 skills especializadas (risco, IA, mercado, etc.) |

## Princípios

- **Preservação de capital** é prioridade absoluta
- **Consistência > ganhos pontuais**
- Evolução **incremental e versionada**
- Decisões **explícitas > implícitas**
- Falhas devem ser **contidas, nunca escondidas**
- Em caso de dúvida: **não operar é uma decisão válida**

## Licença

Proprietário. Todos os direitos reservados.
