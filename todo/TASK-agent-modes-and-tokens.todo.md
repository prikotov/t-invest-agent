---
type: feat
created: 2026-07-04
value: V4
complexity: C2
priority: P0
cost_plan:
cost_fact:
depends_on:
epic: EPIC-agent-safe-trading-cli
author: Владелец проекта (pi)
assignee:
branch:
pr:
status: todo
---

# TASK-agent-modes-and-tokens: Режимы доступа и три токена

## 0. Простое описание (Human Brief)

### Проблема простыми словами (Problem)
- Сейчас в `t-invest-core` один токен `TINKOFF_TOKEN` на все операции — нельзя различить «песочница», «только чтение», «полный доступ».
- Из-за этого нет безопасной модели: один и тот же токен может и читать, и торговать.

### Варианты или путь решения (Solution Sketch)
- Ввести 3 режима с отдельными токенами: `sandbox` (`T_INVEST_TOKEN_SANDBOX`), `readonly` (`T_INVEST_TOKEN_READONLY`), `full` (`T_INVEST_TOKEN_FULL`).
- Источник токенов — единый файл (например `~/.config/tinvest/.env` или локальный `.env.local`), переменные окружения имеют приоритет.
- Активный режим выбирается и хранится персистентно (см. TASK-agent-sandbox-and-session-cli).

### Ожидаемый результат (Expected Result)
- У каждого режима свой токен; без токена для режима — ошибка `APP_TINVEST_TOKEN_MISSING`.
- Команды выполняются строго в активном режиме; `--mode` должен совпадать с активным.

## 1. Concept and Goal (Концепция и цель)

### Story (User Story)
> Как инвестор, я хочу раздельные токены для чтения и торговли, чтобы доступ по принципу наименьших привилегий.

### Goal (Цель по SMART)
Ввести 3 режима/токена в `t-invest-core`, настроить конфигурацию и резолв токена по режиму. Измеримо: unit-тест на резолв токена для каждого режима + ошибку при отсутствии.

## 2. Context and Scope (Контекст и границы)

- **Где делаем:** `t-invest-core/config/`, `src/Component/TInvest/`, конфигурация env.
- **Границы (Out of Scope):** гейт торговли — отдельная задача (TASK-agent-trading-gate).

## 3. Requirements (Требования, MoSCoW)

### 🔴 Must Have (Обязательно)
- [ ] 3 env-переменных: `T_INVEST_TOKEN_SANDBOX/READONLY/FULL`.
- [ ] Резолв токена по режиму; ошибка `APP_TINVEST_TOKEN_MISSING` при отсутствии.
- [ ] Обратная совместимость: старый `TINKOFF_TOKEN` deprecated, но работает как `full`.

### ⚫ Won't Have (Не будем делать)
- [ ] Хранение токенов в БД.
- [ ] Шифрование токенов в покое.

## 4. Implementation Plan (План реализации)

1. [ ] Обновить конфигурацию env в `t-invest-core/config/`.
2. [ ] Реализовать `TokenResolver`/`Mode` enum.
3. [ ] Unit-тесты на резолв.

## 5. Definition of Done (Критерии приёмки)

- [ ] Тесты на все 3 режима + отсутствие токена.
- [ ] `composer stan` зелёный.

## 6. Verification (Самопроверка)

```bash
composer test
php vendor/bin/todo-md-validate todo/TASK-agent-modes-and-tokens.todo.md
```

## 7. Risks and Dependencies (Риски и зависимости)

- Миграция существующих пользователей с `TINKOFF_TOKEN` — описать в changelog.

## 8. Sources (Источники)

- [ ] [`docs/COMPETITOR-ANALYSIS-t-invest-skill.md`](../docs/COMPETITOR-ANALYSIS-t-invest-skill.md)

## 9. Comments (Комментарии)

Зависимость: последующие торговые задачи опираются на эту.

## Change History (История изменений)

| Дата | Автор (роль) | Изменение |
| :--- | :--- | :--- |
| 2026-07-04 | Владелец проекта (pi) | Создание задачи |
