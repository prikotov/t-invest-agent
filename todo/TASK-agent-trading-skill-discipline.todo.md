---
type: docs
created: 2026-07-04
value: V3
complexity: C1
priority: P0
cost_plan:
cost_fact:
depends_on: TASK-agent-order-cli, TASK-agent-stop-order-cli, TASK-agent-sandbox-and-session-cli
epic: EPIC-agent-safe-trading-cli
author: Владелец проекта (pi)
assignee:
branch:
pr:
status: todo
---

# TASK-agent-trading-skill-discipline: Дисциплина режимов и торговая дисциплина в SKILL.md

## 0. Простое описание (Human Brief)

### Проблема простыми словами (Problem)
- `skills/tinvest/SKILL.md` не описывает выбор режима, правила подтверждения сделок, должную осмотрительность.
- Агент не знает, как безопасно вести себя с торговлей.

### Варианты или путь решения (Solution Sketch)
- Перенять дисциплину `SKILL.md` конкурента: стартовый выбор режима (по умолчанию `readonly`), торговая дисциплина (preview → подтверждение → заявка), идемпотентность, должная осмотрительность, правила подачи данных.

### Ожидаемый результат (Expected Result)
- Агент стабильно спрашивает режим в начале диалога и не торгует без подтверждения.

## 1. Concept and Goal (Концепция и цель)

### Story (User Story)
> Как пользователь, я хочу, чтобы агент вёл себя предсказуемо и безопасно со счётом.

### Goal (Цель по SMART)
Обновить `skills/tinvest/SKILL.md` разделами дисциплины. Измеримо: прохождение эвалов на стартовый вопрос и подтверждение сделки.

## 2. Context and Scope (Контекст и границы)

- **Где делаем:** `t-invest-agent/skills/tinvest/SKILL.md`.

## 3. Requirements (Требования, MoSCoW)

### 🔴 Must Have (Обязательно)
- [ ] Раздел выбора режима при активации.
- [ ] Раздел торговой дисциплины.
- [ ] Дисклеймер ИИР по контексту.

### ⚫ Won't Have (Не будем делать)
- [ ] Полная переработка всех навыков (это EPIC-agent-cleanup-and-strengths).

## 4. Implementation Plan (План реализации)

1. [ ] Дописать разделы в `SKILL.md`.
2. [ ] Сверить с `references/json-fields.md` (после его создания).

## 5. Definition of Done (Критерии приёмки)

- [ ] Разделы присутствуют и согласованы с командами.

## 6. Verification (Самопроверка)

```bash
php vendor/bin/todo-md-validate todo/TASK-agent-trading-skill-discipline.todo.md
```

## 7. Risks and Dependencies (Риски и зависимости)

- Зависит от торговых CLI-задач.

## 8. Sources (Источники)

- [ ] [`docs/COMPETITOR-ANALYSIS-t-invest-skill.md`](../docs/COMPETITOR-ANALYSIS-t-invest-skill.md)
- [ ] Эталон `SKILL.md` конкурента

## 9. Comments (Комментарии)

## Change History (История изменений)

| Дата | Автор (роль) | Изменение |
| :--- | :--- | :--- |
| 2026-07-04 | Владелец проекта (pi) | Создание задачи |
