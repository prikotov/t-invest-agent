---
type: feat
created: 2026-07-04
value: V3
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

# TASK-agent-liquidity-guard: Гард ликвидности перед рыночной заявкой

## 0. Простое описание (Human Brief)

### Проблема простыми словами (Problem)
- Market-ордер по малоликвидной бумаге с широким спредом может сильно ударить по цене.

### Варианты или путь решения (Solution Sketch)
- Перед реальной рыночной заявкой проверять стакан/спред; на неликвиде/широком спреде блокировать и предлагать лимитную.

### Ожидаемый результат (Expected Result)
- N’est pas de slippage évité на неликвиде: заявка блокируется с понятным сообщением.

## 1. Concept and Goal (Концепция и цель)

### Story (User Story)
> Как инвестор, я хочу защиту от исполнения market-ордера по плохой цене на неликвиде.

### Goal (Цель по SMART)
Реализовать `LiquidityGuard` на базе `market:orderbook`. Измеримо: тест на широкий/узкий спред.

## 2. Context and Scope (Контекст и границы)

- **Где делаем:** `t-invest-core/src/Service/Trading/`.

## 3. Requirements (Требования, MoSCoW)

### 🔴 Must Have (Обязательно)
- [ ] Проверка спреда перед market-ордером, блокировка при превышении порога.

### ⚫ Won't Have (Не будем делать)
- [ ] Умное разбиение крупной заявки (iceberg/TWAP).

## 4. Implementation Plan (План реализации)

1. [ ] `LiquidityGuard` + порог спреда.
2. [ ] Тесты.

## 5. Definition of Done (Критерии приёмки)

- [ ] Тесты на блокировку/пропуск.

## 6. Verification (Самопроверка)

```bash
composer test
php vendor/bin/todo-md-validate todo/TASK-agent-liquidity-guard.todo.md
```

## 7. Risks and Dependencies (Риски и зависимости)

- Зависит от TASK-agent-trading-gate.

## 8. Sources (Источники)

- [ ] [`docs/COMPETITOR-ANALYSIS-t-invest-skill.md`](../docs/COMPETITOR-ANALYSIS-t-invest-skill.md)

## 9. Comments (Комментарии)

## Change History (История изменений)

| Дата | Автор (роль) | Изменение |
| :--- | :--- | :--- |
| 2026-07-04 | Владелец проекта (pi) | Создание задачи |
