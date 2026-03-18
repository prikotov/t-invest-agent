---
name: monitor
description: Мониторинг цен и расписаний — алерты и автоматические проверки.
---

# Monitor

## Когда использовать

- Уведомление о достижении цены
- Запуск анализа по расписанию
- Автоматическая проверка условий рецепта

## Как использовать

**Шаг 1:** Создать ценовой монитор

```bash
    php scripts/monitor.php create price --ticker=TICKER --level=PRICE --direction=UP|DOWN [--recipe=ID]
```

Опции:

| Опция       | Описание                      | Значения    | Обязательно |
|-------------|------------------------------|-------------|-------------|
| --ticker    | Тикер инструмента             | строка      | Да          |
| --level     | Целевой уровень цены          | число       | Да          |
| --direction | Направление пробоя            | UP, DOWN    | Да          |
| --recipe    | ID связанного рецепта         | строка      | Нет         |
| --action    | Команда при срабатывании      | строка      | Нет         |

Примеры:

```bash
    # Алерт на пробой уровня
    php scripts/monitor.php create price --ticker=SBER --level=260 --direction=UP

    # С привязкой к рецепту
    php scripts/monitor.php create price --ticker=SBER --level=250 --direction=DOWN --recipe=recipe-2024-03-18-001
```

**Шаг 2:** Создать scheduled монитор

```bash
    php scripts/monitor.php create schedule --cron=EXPR --prompt=PROMPT
```

Опции:

| Опция    | Описание                          | Обязательно |
|----------|-----------------------------------|-------------|
| --cron   | Cron-расписание                   | Да          |
| --prompt | Промпт для выполнения             | Да          |

Примеры:

```bash
    # Ежедневный утренний анализ
    php scripts/monitor.php create schedule --cron="0 9 * * 1-5" --prompt="@morning-check"

    # Еженедельный отчёт
    php scripts/monitor.php create schedule --cron="0 18 * * 5" --prompt="@weekly-report"
```

**Шаг 3:** Список мониторов

```bash
    php scripts/monitor.php list [--type=TYPE]
```

Опции:

| Опция  | Описание          | Значения        |
|--------|-------------------|-----------------|
| --type | Фильтр по типу    | price, schedule |

Примеры:

```bash
    php scripts/monitor.php list
    php scripts/monitor.php list --type=price
```

**Шаг 4:** Проверить все мониторы

```bash
    php scripts/monitor.php check
```

Проверяет все активные ценовые мониторы через MOEX API и выводит сработавшие.

**Шаг 5:** Выполнить монитор вручную

```bash
    php scripts/monitor.php run <id>
```

Примеры:

```bash
    php scripts/monitor.php run monitor-001
```

**Шаг 6:** Удалить монитор

```bash
    php scripts/monitor.php delete <id>
```

## Результат

Файл: `data/monitors/monitor-NNN.json`

Поля (price):

| Поле        | Описание                    |
|-------------|----------------------------|
| id          | Идентификатор монитора      |
| type        | price                       |
| ticker      | Тикер инструмента           |
| level       | Целевой уровень             |
| direction   | UP или DOWN                 |
| recipe_id   | Связанный рецепт            |
| status      | ACTIVE, TRIGGERED           |
| created_at  | Дата создания               |
| triggered_at| Дата срабатывания           |

Поля (schedule):

| Поле       | Описание                    |
|------------|----------------------------|
| id         | Идентификатор монитора      |
| type       | schedule                    |
| cron       | Расписание                  |
| prompt     | Промпт для выполнения       |
| status     | ACTIVE                      |
| last_run   | Последний запуск            |
| next_run   | Следующий запуск            |

Не является инвестиционной рекомендацией.
