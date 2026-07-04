---
type: feat
created: 2026-07-04
value: V3
complexity: C2
priority: P0
cost_plan:
cost_fact:
depends_on: TASK-agent-order-cli
epic: EPIC-agent-safe-trading-cli
author: Владелец проекта (pi)
assignee:
branch:
pr:
status: todo
---

# TASK-agent-stop-order-cli: Стоп-заявки set/list/cancel

## 0. Простое описание (Human Brief)

### Проблема простыми словами (Problem)
- Нет управления стоп-заявками (take-profit/stop-loss/stop-limit) из CLI.

### Варианты или путь решения (Solution Sketch)
- `stop-order set/list/cancel`; бессрочные стопы с `--type`, `--stop-price`, `--price`.

### Ожидаемый результат (Expected Result)
- Пользователь ставит и снимает стопы через агента.

## 1. Concept and Goal (Концепция и цель)

### Story (User Story)
> Как инвестор, я хочу ставить стоп-заявки для защиты позиций.

### Goal (Цель по SMART)
3 подкоманды `stop-order:*`. Измеримо: тест на постановку/снятие стопа в sandbox.

## 2. Context and Scope (Контекст и границы)

- **Где делаем:** `t-invest-core/src/Command/Trading/`.

## 3. Requirements (Требования, MoSCoW)

### 🔴 Must Have (Обязательно)
- [ ] `set` (take-profit/stop-loss/stop-limit), `list`, `cancel`.

### ⚫ Won't Have (Не будем делать)
- [ ] Трейлинг-стопы (если API не отдаёт).

## 4. Implementation Plan (План реализации)

1. [ ] Команды + DTO.
2. [ ] Тесты.

## 5. Definition of Done (Критерии приёмки)

- [ ] Тест на цикл стопа зелёный.

## 6. Verification (Самопроверка)

```bash
composer test
php vendor/bin/todo-md-validate todo/TASK-agent-stop-order-cli.todo.md
```

## 7. Risks and Dependencies (Риски и зависимости)

- Зависит от TASK-agent-order-cli.

## 8. Sources (Источники)

- [ ] [`docs/COMPETITOR-ANALYSIS-t-invest-skill.md`](../docs/COMPETITOR-ANALYSIS-t-invest-skill.md)

## 9. Comments (Комментарии)

## Change History (История изменений)

| Дата | Автор (роль) | Изменение |
| :--- | :--- | :--- |
| 2026-07-04 | Владелец проекта (pi) | Создание задачи |
