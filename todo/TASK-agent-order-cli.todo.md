---
type: feat
created: 2026-07-04
value: V4
complexity: C2
priority: P0
cost_plan:
cost_fact:
depends_on: TASK-agent-liquidity-guard, TASK-agent-idempotency-and-audit
epic: EPIC-agent-safe-trading-cli
author: Владелец проекта (pi)
assignee:
branch:
pr:
status: todo
---

# TASK-agent-order-cli: Команды order preview/buy/sell/list/status/cancel/replace

## 0. Простое описание (Human Brief)

### Проблема простыми словами (Problem)
- Код `OrdersService` есть, но CLI-команд нет — торговать из агента нельзя.

### Варианты или путь решения (Solution Sketch)
- `order preview/buy/sell/list/status/cancel/replace`; рыночная (без `--price`) и лимитная заявки.
- `-q` = ЛОТЫ (с пересчётом «штук»), `--order-id` обязателен, `--confirm` в full.

### Ожидаемый результат (Expected Result)
- Полный цикл заявки из CLI с предпросмотром, подтверждением, заменой/отменой.

## 1. Concept and Goal (Концепция и цель)

### Story (User Story)
> Как пользователь, я хочу выставлять/менять/отменять заявки командами агента.

### Goal (Цель по SMART)
7 подкоманд `order:*` поверх `OrdersServiceComponent` + гейт. Измеримо: интеграционный тест в sandbox на полный цикл.

## 2. Context and Scope (Контекст и границы)

- **Где делаем:** `t-invest-core/src/Command/Trading/`.

## 3. Requirements (Требования, MoSCoW)

### 🔴 Must Have (Обязательно)
- [ ] `preview`, `buy`, `sell`, `list`, `status`, `cancel`, `replace`.
- [ ] Рыночная/лимитная, `-q` лоты, `--order-id`, `--confirm`.

### ⚫ Won't Have (Не будем делать)
- [ ] Маржинальные сделки.

## 4. Implementation Plan (План реализации)

1. [ ] Команды + DTO + форматированный вывод.
2. [ ] Интеграция с гейтом/гардом/аудитом.
3. [ ] Тесты (sandbox).

## 5. Definition of Done (Критерии приёмки)

- [ ] Интеграционный тест полного цикла зелёный.

## 6. Verification (Самопроверка)

```bash
composer test
php vendor/bin/todo-md-validate todo/TASK-agent-order-cli.todo.md
```

## 7. Risks and Dependencies (Риски и зависимости)

- Зависит от гейта, гарда, аудита.

## 8. Sources (Источники)

- [ ] [`docs/COMPETITOR-ANALYSIS-t-invest-skill.md`](../docs/COMPETITOR-ANALYSIS-t-invest-skill.md)

## 9. Comments (Комментарии)

## Change History (История изменений)

| Дата | Автор (роль) | Изменение |
| :--- | :--- | :--- |
| 2026-07-04 | Владелец проекта (pi) | Создание задачи |
