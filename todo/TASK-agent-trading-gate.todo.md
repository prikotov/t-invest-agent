---
type: feat
created: 2026-07-04
value: V4
complexity: C2
priority: P0
cost_plan:
cost_fact:
depends_on: TASK-agent-modes-and-tokens
epic: EPIC-agent-safe-trading-cli
author: Владелец проекта (pi)
assignee:
branch:
pr:
status: todo
---

# TASK-agent-trading-gate: Гейт реальных сделок, подтверждение и STONKS

## 0. Простое описание (Human Brief)

### Проблема простыми словами (Problem)
- Само наличие full-токена сейчас разрешает торговать. Нет гейта: сделка может уйти случайно или автономно.
- Нет обязательного подтверждения каждой сделки пользователем.

### Варианты или путь решения (Solution Sketch)
- Лестница гейтов в окружении: `readonly` → сделка запрещена; `sandbox` → свободно; `full` без `T_INVEST_ALLOW_TRADING` → `APP_TINVEST_TRADING_DISABLED`; `full` + флаг → требует `--confirm`; `full` + `T_INVEST_STONKS_MODE` → автономно.
- Гейт работает в коде, ДО обращения к API.

### Ожидаемый результат (Expected Result)
- Невозможно случайно торговать на реальные деньги: нужен и флаг env, и `--confirm`.

## 1. Concept and Goal (Концепция и цель)

### Story (User Story)
> Как инвестор, я хочу, чтобы реальные сделки были технически невозможны без моего явного подтверждения.

### Goal (Цель по SMART)
Реализовать `TradingGate` со всеми переходами гейта. Измеримо: unit-тесты на каждый путь отказа/разрешения.

## 2. Context and Scope (Контекст и границы)

- **Где делаем:** `t-invest-core/src/Service/Trading/`.
- **Границы:** не делаем шифрование/криптографию — это «защита штатных путей».

## 3. Requirements (Требования, MoSCoW)

### 🔴 Must Have (Обязательно)
- [ ] Все 5 ветвей гейта с кодами `APP_TINVEST_TRADING_FORBIDDEN/DISABLED/CONFIRM_REQUIRED`.
- [ ] `--confirm` проверяется в коде.

### ⚫ Won't Have (Не будем делать)
- [ ] Кастомные политики beyond гейта.

## 4. Implementation Plan (План реализации)

1. [ ] `TradingGate` сервис + режим/флаг/confirm.
2. [ ] Коды ошибок на русском.
3. [ ] Unit-тесты на все ветви.

## 5. Definition of Done (Критерии приёмки)

- [ ] 5 тестов на ветви гейта зелёные.

## 6. Verification (Самопроверка)

```bash
composer test
php vendor/bin/todo-md-validate todo/TASK-agent-trading-gate.todo.md
```

## 7. Risks and Dependencies (Риски и зависимости)

- Зависит от TASK-agent-modes-and-tokens.

## 8. Sources (Источники)

- [ ] [`docs/COMPETITOR-ANALYSIS-t-invest-skill.md`](../docs/COMPETITOR-ANALYSIS-t-invest-skill.md)

## 9. Comments (Комментарии)

## Change History (История изменений)

| Дата | Автор (роль) | Изменение |
| :--- | :--- | :--- |
| 2026-07-04 | Владелец проекта (pi) | Создание задачи |
