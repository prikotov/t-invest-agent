---
type: feat
created: 2026-07-04
value: V3
complexity: C1
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

# TASK-agent-sandbox-and-session-cli: sandbox init и session start/status/end

## 0. Простое описание (Human Brief)

### Проблема простыми словами (Problem)
- Нет команды создать виртуальный счёт в песочнице и зафиксировать активный режим работы.

### Варианты или путь решения (Solution Sketch)
- `sandbox init [--amount <руб>]` — открыть и пополнить виртуальный счёт.
- `session start [--mode m]` / `status` / `end` — зафиксировать активный режим (по умолчанию `readonly`).

### Ожидаемый результат (Expected Result)
- До выбора режима команды с данными не выполняются (`APP_TINVEST_SESSION_REQUIRED`).

## 1. Concept and Goal (Концепция и цель)

### Story (User Story)
> Как пользователь, я хочу тренироваться в песочнице и явно выбирать режим работы со счётом.

### Goal (Цель по SMART)
Команды `sandbox init`, `session start/status/end`. Измеримо: тест на блокировку без сессии.

## 2. Context and Scope (Контекст и границы)

- **Где делаем:** `t-invest-core/src/Command/Session/`, `src/Command/Sandbox/`.

## 3. Requirements (Требования, MoSCoW)

### 🔴 Must Have (Обязательно)
- [ ] `sandbox init`, `session start/status/end`.
- [ ] `APP_TINVEST_SESSION_REQUIRED` без активной сессии.

### 🟢 Could Have (Опционально)
- [ ] `TINVEST_SESSION_ID` для параллельных агентов.

### ⚫ Won't Have (Не будем делать)
- [ ] Хранение режима в БД.

## 4. Implementation Plan (План реализации)

1. [ ] `Session` сервис (персистентное хранилище режима).
2. [ ] Команды.
3. [ ] Тесты.

## 5. Definition of Done (Критерии приёмки)

- [ ] Тест на старт/статус/блокировку зелёный.

## 6. Verification (Самопроверка)

```bash
composer test
php vendor/bin/todo-md-validate todo/TASK-agent-sandbox-and-session-cli.todo.md
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
