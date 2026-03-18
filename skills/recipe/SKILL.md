---
name: recipe
description: Управление торговыми рецептами — идеи сделок с уровнями входа, стопа и цели.
---

# Recipe

## Когда использовать

- Планирование новой сделки
- Хранение идей до момента исполнения
- Отслеживание статуса торговых идей

## Как использовать

**Шаг 1:** Создать рецепт

```bash
    php scripts/recipe.php create --ticker=TICKER --direction=LONG|SHORT --entry=PRICE --target=PRICE --stop=PRICE [--thesis=...]
```

Опции:

| Опция      | Описание                    | Значения    | Обязательно |
|------------|----------------------------|-------------|-------------|
| --ticker   | Тикер инструмента           | строка      | Да          |
| --direction| Направление сделки          | LONG, SHORT | Да          |
| --entry    | Цена входа                  | число       | Да          |
| --target   | Целевая цена (тейк)         | число       | Да          |
| --stop     | Стоп-лосс                   | число       | Да          |
| --thesis   | Тезис идеи (обоснование)    | строка      | Нет         |

Примеры:

```bash
    # Long рецепт на SBER
    php scripts/recipe.php create --ticker=SBER --direction=LONG --entry=250 --target=300 --stop=240

    # С тезисом
    php scripts/recipe.php create --ticker=GAZP --direction=LONG --entry=170 --target=200 --stop=160 --thesis="Пробой сопротивления"
```

**Шаг 2:** Посмотреть список рецептов

```bash
    php scripts/recipe.php list [--status=STATUS]
```

Опции:

| Опция   | Описание                        | Значения                          |
|---------|--------------------------------|-----------------------------------|
| --status| Фильтр по статусу               | ACTIVE, TRIGGERED, CANCELLED, DONE|

Примеры:

```bash
    # Все рецепты
    php scripts/recipe.php list

    # Только активные
    php scripts/recipe.php list --status=ACTIVE
```

**Шаг 3:** Показать рецепт

```bash
    php scripts/recipe.php show <id>
```

Примеры:

```bash
    php scripts/recipe.php show recipe-2024-03-18-001
```

**Шаг 4:** Обновить статус

```bash
    php scripts/recipe.php update <id> --status=STATUS [--note=...]
```

Опции:

| Опция   | Описание             | Значения                          |
|---------|---------------------|-----------------------------------|
| --status| Новый статус         | ACTIVE, TRIGGERED, CANCELLED, DONE|
| --note  | Заметка к обновлению | строка                            |

Примеры:

```bash
    php scripts/recipe.php update recipe-2024-03-18-001 --status=TRIGGERED
    php scripts/recipe.php update recipe-2024-03-18-001 --status=CANCELLED --note="Пробой уровня"
```

**Шаг 5:** Удалить рецепт

```bash
    php scripts/recipe.php delete <id>
```

## Результат

Файл: `data/recipes/recipe-YYYY-MM-DD-NNN.json`

Поля:

| Поле      | Описание                    |
|-----------|----------------------------|
| id        | Идентификатор рецепта       |
| ticker    | Тикер инструмента           |
| direction | LONG или SHORT              |
| entry     | Цена входа                  |
| target    | Целевая цена                |
| stop      | Стоп-лосс                   |
| rr        | Risk:Reward ratio           |
| risk_pct  | Риск в %                    |
| reward_pct| Потенциал в %               |
| thesis    | Обоснование идеи            |
| status    | Статус рецепта              |
| created_at| Дата создания               |

Не является инвестиционной рекомендацией.
