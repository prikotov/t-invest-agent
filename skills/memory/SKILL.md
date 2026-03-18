---
name: memory
description: Память агента — хранение предпочтений и профиля пользователя.
---

# Memory

## Когда использовать

- Сохранение предпочтений инвестора
- Хранение профиля риска
- Запоминание избранных тикеров и секторов

## Как использовать

**Шаг 1:** Показать профиль

```bash
    php scripts/memory.php profile
```

Выводит текущий профиль пользователя или значения по умолчанию.

**Шаг 2:** Получить значение

```bash
    php scripts/memory.php get <key>
```

Примеры:

```bash
    php scripts/memory.php get risk_tolerance
    php scripts/memory.php get favorite_sectors
```

**Шаг 3:** Установить значение

```bash
    php scripts/memory.php set <key> <value>
```

Примеры:

```bash
    php scripts/memory.php set risk_tolerance moderate
    php scripts/memory.php set horizon long-term
    php scripts/memory.php set max_position_pct 15
    php scripts/memory.php set favorite_sectors '["financial","energy"]'
```

**Шаг 4:** Обновить профиль

```bash
    php scripts/memory.php update [--risk=...] [--horizon=...] [--style=...] [--sectors=...] [--max-pos=...]
```

Опции:

| Опция      | Описание                | Значения                                    |
|------------|------------------------|---------------------------------------------|
| --risk     | Толерантность к риску   | conservative, moderate, aggressive          |
| --horizon  | Горизонт инвестирования | short-term, medium-term, long-term          |
| --style    | Стиль инвестирования    | value, growth, dividend, momentum           |
| --sectors  | Избранные секторы       | через запятую                               |
| --max-pos  | Макс. доля позиции      | число в %                                   |

Примеры:

```bash
    php scripts/memory.php update --risk=aggressive --horizon=short-term
    php scripts/memory.php update --style=dividend --sectors=financial,energy --max-pos=15
```

**Шаг 5:** Очистить память

```bash
    php scripts/memory.php clear
```

## Результат

Файл: `data/memory/user.json`

Поля:

| Поле              | Описание                                       | Значения                            |
|-------------------|------------------------------------------------|-------------------------------------|
| risk_tolerance    | Толерантность к риску                          | conservative, moderate, aggressive  |
| horizon           | Инвестиционный горизонт                        | short-term, medium-term, long-term  |
| style             | Стиль инвестирования                           | value, growth, dividend, momentum   |
| favorite_sectors  | Избранные секторы                              | массив строк                        |
| avoid_sectors     | Секторы для исключения                         | массив строк                        |
| max_position_pct  | Максимальная доля одной позиции в портфеле     | число                               |
| updated_at        | Дата последнего обновления                     | ISO 8601                            |

## Значения по умолчанию

```json
{
  "risk_tolerance": "moderate",
  "horizon": "long-term",
  "style": "value",
  "favorite_sectors": [],
  "avoid_sectors": [],
  "max_position_pct": 10
}
```
