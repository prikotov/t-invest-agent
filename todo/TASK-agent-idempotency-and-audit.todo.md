---
type: feat
created: 2026-07-04
value: V4
complexity: C2
priority: P0
cost_plan:
cost_fact:
depends_on: TASK-agent-trading-gate
epic: EPIC-agent-safe-trading-cli
author: Владелец проекта (pi)
assignee:
branch:
pr:
status: todo
---

# TASK-agent-idempotency-and-audit: Идемпотентность заявок и аудит-журнал

## 0. Простое описание (Human Brief)

### Проблема простыми словами (Problem)
- При сетевом сбое/таймауте повтор заявки может задвоить её на стороне брокера.
- Нет истории мутаций: непонятно, какие заявки агент выставлял.

### Варианты или путь решения (Solution Sketch)
- Обязательный ключ идемпотентности `--order-id` (UUID) на каждую мутацию; повтор после сбоя — строго с тем же ключом.
- Печать ключа в stderr ДО отправки.
- Аудит-журнал `trades.log`: время, режим, бумага, лоты, цена, номер заявки.

### Ожидаемый результат (Expected Result)
- Повтор заявки после сбоя не дублирует сделку; есть полный аудит мутаций.

## 1. Concept and Goal (Концепция и цель)

### Story (User Story)
> Как инвестор, я хочу защиту от дублирования заявок при сбое и журнал всех сделок агента.

### Goal (Цель по SMART)
Передача `order_id` в `OrdersServiceComponent` + запись в `trades.log`. Измеримо: тест на повтор с тем же `order_id` не создаёт вторую заявку.

## 2. Context and Scope (Контекст и границы)

- **Где делаем:** `t-invest-core/src/Service/Trading/` (аудит), обёртка команд.

## 3. Requirements (Требования, MoSCoW)

### 🔴 Must Have (Обязательно)
- [ ] `--order-id` обязателен для мутаций.
- [ ] Аудит-журнал `trades.log` (реальные + sandbox).
- [ ] Печать ключа в stderr до отправки.

### ⚫ Won't Have (Не будем делать)
- [ ] Ротация журнала (отдельная chore-задача).

## 4. Implementation Plan (План реализации)

1. [ ] Обёртка `OrdersServiceComponent` с `order_id`.
2. [ ] `AuditLogger` → `trades.log`.
3. [ ] Тесты.

## 5. Definition of Done (Критерии приёмки)

- [ ] Тест на идемпотентность + запись аудита.

## 6. Verification (Самопроверка)

```bash
composer test
php vendor/bin/todo-md-validate todo/TASK-agent-idempotency-and-audit.todo.md
```

## 7. Risks and Dependencies (Риски и зависимости)

- TTL идемпотентности на стороне API — задокументировать.

## 8. Sources (Источники)

- [ ] [`docs/COMPETITOR-ANALYSIS-t-invest-skill.md`](../docs/COMPETITOR-ANALYSIS-t-invest-skill.md)

## 9. Comments (Комментарии)

## Change History (История изменений)

| Дата | Автор (роль) | Изменение |
| :--- | :--- | :--- |
| 2026-07-04 | Владелец проекта (pi) | Создание задачи |
