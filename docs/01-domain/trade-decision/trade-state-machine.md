# Domínio – Diagrama de Estados do Trade (UC-01)

## Objetivo
Definir explicitamente os **estados possíveis de um trade** dentro do sistema, desde a análise até o encerramento, garantindo previsibilidade, auditabilidade e ausência de estados implícitos.

---

## Entidade Central
**TradeDecision (Aggregate Root)**

O diagrama descreve o ciclo de vida lógico de uma decisão de trade, não a execução operacional da ordem.

---

## Estados

### 1. CREATED
**Descrição:**
- A análise foi iniciada
- Dados de mercado e perfil do usuário foram coletados

**Permite transição para:**
- ANALYZED

---

### 2. ANALYZED
**Descrição:**
- Tendência identificada
- Pullback avaliado
- Setup validado tecnicamente

**Permite transição para:**
- RISK_VALIDATED
- BLOCKED

---

### 3. RISK_VALIDATED
**Descrição:**
- Risco calculado
- Exposição validada
- Compatível com perfil do usuário

**Permite transição para:**
- APPROVED
- BLOCKED

---

### 4. APPROVED
**Descrição:**
- Trade permitido
- Sugestões de entrada, stop e alvo definidas

**Permite transição para:**
- EXECUTED (externo)
- EXPIRED

---

### 5. BLOCKED
**Descrição:**
- Trade rejeitado por regra técnica ou de risco
- Motivos registrados

**Estado final**

---

### 6. EXECUTED (externo ao domínio)
**Descrição:**
- Trade executado manualmente pelo usuário ou broker
- Sistema apenas registra o fato

**Permite transição para:**
- CLOSED

---

### 7. EXPIRED
**Descrição:**
- Setup perdeu validade (tempo, preço, contexto)

**Estado final**

---

### 8. CLOSED
**Descrição:**
- Trade encerrado (gain ou loss)
- Resultado registrado no journal

**Estado final**

---

## Transições Proibidas
- BLOCKED → qualquer estado
- EXPIRED → qualquer estado
- CLOSED → qualquer estado
- APPROVED → ANALYZED

---

## Invariantes Importantes
- Nenhum trade executado sem estado APPROVED
- Nenhuma decisão reavaliada após BLOCKED
- EXECUTED não pertence ao core domain

---

## Observação Crítica
O sistema **não força trades**. Ele **autoriza contextos**. A execução é sempre externa e consciente.

