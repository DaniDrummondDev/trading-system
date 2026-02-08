# CI/CD Pipeline — Trading System

## Visão Geral

O Trading System utiliza **GitHub Actions** para automação de qualidade,
segurança e deploy. O pipeline segue o princípio **fail-fast**: checks
baratos e rápidos rodam primeiro; checks caros (com banco de dados) rodam
somente após os primeiros passarem.

---

## Arquitetura do Pipeline

### Workflows

| Workflow | Arquivo | Trigger | Propósito |
|----------|---------|---------|-----------|
| CI Pipeline | `ci.yml` | Push para `main`, PRs | Lint, análise, testes, coverage |
| Security Audit | `security.yml` | Segunda 08:00 UTC, manual | Auditoria de vulnerabilidades |
| Deploy | `deploy.yml` | Manual, tags `v*.*.*` | Build e deploy (staging/production) |

### Fluxo do CI Pipeline

```
     ┌──────┐  ┌─────────────────┐  ┌────────────────┐
     │ lint │  │ static-analysis │  │ security-audit │
     └──┬───┘  └───────┬─────────┘  └────────────────┘
        └──────┬───────┘              (independente)
               │
     ┌─────────┴──────────┐
     │                    │
 ┌───▼──────┐      ┌─────▼─────┐
 │unit-tests│      │arch-tests │
 └───┬──────┘      └───────────┘
     │
 ┌───┴──────────┐
 │              │
 ┌▼───────────┐ ┌▼────────────┐
 │integration │ │feature-tests│
 └─────┬──────┘ └──────┬──────┘
       └───────┬───────┘
        ┌──────▼────────┐
        │coverage-report│
        └───────────────┘
```

**Tempo estimado:** ~4-6 minutos (pipeline completo)

---

## Jobs Detalhados

### 1. Lint (Pint) — ~30s

Verifica formatação de código usando [Laravel Pint](https://laravel.com/docs/pint).

```bash
vendor/bin/pint --test
```

- Não requer serviços externos
- Falha rápida se código não está formatado
- Corrigir localmente: `composer lint:fix`

### 2. Static Analysis (PHPStan) — ~60s

Análise estática com [Larastan](https://github.com/larastan/larastan) (PHPStan + Laravel).

```bash
vendor/bin/phpstan analyse --memory-limit=512M
```

- Nível 5 (progressão planejada até nível 8+)
- Baseline em `phpstan-baseline.neon` para erros pré-existentes
- Configuração em `phpstan.neon`

### 3. Security Audit — ~15s

Verifica vulnerabilidades conhecidas em dependências.

```bash
composer audit
```

- Roda em paralelo com lint e análise estática
- Também roda semanalmente via workflow `security.yml`

### 4. Unit Tests — ~30s

Testes unitários do Domain e Application layers.

```bash
vendor/bin/pest --testsuite=Unit
```

- **Sem banco de dados** (SQLite :memory: quando necessário)
- **Sem boot do Laravel** para testes de Domain
- Coverage gerado com `pcov`
- Depende de: lint + static-analysis passarem

### 5. Architecture Tests — ~20s

Testes de conformidade arquitetural via `pest-plugin-arch`.

```bash
vendor/bin/pest --group=arch
```

- Valida regras da Clean Architecture:
  - Domain não depende de Laravel, Infrastructure ou Interfaces
  - Application não depende de Infrastructure ou Interfaces
  - Controllers não contêm lógica de negócio
- Depende de: lint + static-analysis passarem

### 6. Integration Tests — ~2-3min

Testes de integração do Infrastructure layer.

```bash
vendor/bin/pest --testsuite=Integration
```

- **Requer PostgreSQL 18 + pgvector + Redis**
- Testa repositórios, Event Bus, Market Data
- Migrations executadas antes dos testes
- Extensões pgvector e uuid-ossp habilitadas
- Depende de: unit-tests passarem

### 7. Feature Tests — ~2-3min

Testes da API REST completa.

```bash
vendor/bin/pest --testsuite=Feature
```

- **Requer PostgreSQL 18 + pgvector + Redis**
- Testa endpoints HTTP, autenticação, validação
- Full Laravel boot
- Depende de: unit-tests passarem

### 8. Coverage Report

Consolida relatórios de coverage de todos os test suites.
Disponível como artefato no GitHub Actions.

---

## Serviços no CI

O pipeline provisiona automaticamente:

| Serviço | Imagem | Porta | Health Check |
|---------|--------|-------|-------------|
| PostgreSQL 18 | `pgvector/pgvector:0.8.1-pg18` | 5432 | `pg_isready` |
| Redis | `redis:alpine` | 6379 | `redis-cli ping` |

Extensões SQL habilitadas automaticamente:
- `vector` (pgvector para embeddings)
- `uuid-ossp` (geração de UUIDs)

---

## Environments de CI

| Arquivo | Uso | Banco |
|---------|-----|-------|
| `.env.ci` | Unit + Arch tests | SQLite :memory: |
| `.env.ci.integration` | Integration + Feature tests | PostgreSQL 18 |

---

## Como Rodar Localmente

Antes de fazer push, rode os checks localmente:

```bash
# Lint (formatação)
docker compose exec app composer lint

# Corrigir formatação
docker compose exec app composer lint:fix

# Análise estática
docker compose exec app composer analyse

# Todos os testes
docker compose exec app composer test

# Testes por suite
docker compose exec app composer test:unit
docker compose exec app composer test:integration
docker compose exec app composer test:feature
docker compose exec app composer test:arch

# Todos os checks do CI de uma vez
docker compose exec app composer ci

# Testes com coverage
docker compose exec app composer test:coverage
```

---

## PHPStan / Larastan

### Configuração

- **Arquivo:** `phpstan.neon`
- **Nível atual:** 5
- **Baseline:** `phpstan-baseline.neon` (erros pré-existentes)

### Progressão de Nível

| Fase do Projeto | Nível PHPStan | Razão |
|-----------------|---------------|-------|
| Fase 0 (atual) | 5 | Base sólida para sistema financeiro |
| Fase 1 | 6 | Union types, checkado após Domain Layer |
| Fase 2 | 7 | Method calls em classes tipadas |
| Fase 5+ | 8+ | Máxima segurança de tipos |

### Gerando Baseline

Se novos erros pré-existentes surgirem (ex: ao adicionar dependências):

```bash
docker compose exec app vendor/bin/phpstan analyse --generate-baseline
```

### Excluindo Falsos Positivos

Adicionar em `phpstan.neon`:

```neon
parameters:
    ignoreErrors:
        - '#Mensagem do erro a ignorar#'
```

---

## Security Audit

### Workflow Semanal

O workflow `security.yml` roda toda segunda-feira às 08:00 UTC.
Também pode ser acionado manualmente via GitHub Actions.

### Relatório

O relatório JSON é salvo como artefato por 30 dias.
Se vulnerabilidades forem encontradas, o workflow falha e aparece
como alerta na aba Actions do repositório.

### Rodar Localmente

```bash
docker compose exec app composer audit
```

---

## Deploy

### Ambientes

| Ambiente | Trigger | Aprovação |
|----------|---------|-----------|
| Staging | Tag `v*.*.*` ou manual | Automática |
| Production | Manual | Requer aprovação via GitHub Environment |

### Fluxo de Deploy

```
Tag v1.0.0 → Build Docker → Push ghcr.io → Deploy Staging → Health Check
                                                    ↓
                                        Manual approval
                                                    ↓
                                          Deploy Production → Health Check
```

### Container Registry

Imagens Docker são publicadas no GitHub Container Registry (ghcr.io):

```
ghcr.io/danidrummonddev/trading-system:latest
ghcr.io/danidrummonddev/trading-system:v1.0.0
ghcr.io/danidrummonddev/trading-system:<sha>
```

---

## Branch Protection (Recomendado)

Configurar em GitHub → Settings → Branches → Branch protection rules:

| Regra | Valor |
|-------|-------|
| Require pull request reviews | 1 reviewer mínimo |
| Dismiss stale approvals | Habilitado |
| Require status checks | `lint`, `static-analysis`, `unit-tests`, `arch-tests`, `integration-tests`, `feature-tests`, `security-audit` |
| Require up to date before merging | Habilitado |
| Require conversation resolution | Habilitado |
| Restrict deletions | Habilitado |
| Allow force pushes | Desabilitado |

---

## Troubleshooting

### Lint falhou

```bash
# Ver o que precisa ser corrigido
docker compose exec app vendor/bin/pint --test

# Corrigir automaticamente
docker compose exec app composer lint:fix
```

### PHPStan falhou

```bash
# Ver erros detalhados
docker compose exec app vendor/bin/phpstan analyse

# Se são erros legítimos de código ainda não implementado,
# gerar nova baseline:
docker compose exec app vendor/bin/phpstan analyse --generate-baseline
```

### Testes de integração falharam

1. Verificar se PostgreSQL e Redis estão rodando:
   ```bash
   docker compose ps
   ```
2. Verificar se extensões estão habilitadas:
   ```bash
   docker compose exec postgres psql -U trading -d trading_system -c "SELECT extname FROM pg_extension;"
   ```
3. Rodar migrations:
   ```bash
   docker compose exec app php artisan migrate:fresh
   ```

### Coverage não gerado

Verificar se a extensão `pcov` está instalada:
```bash
docker compose exec app php -m | grep pcov
```

Se não estiver, adicionar ao Dockerfile:
```dockerfile
RUN pecl install pcov && docker-php-ext-enable pcov
```
