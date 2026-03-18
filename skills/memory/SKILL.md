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

**Шаг 5:** Синхронизировать позиции из портфеля

```bash
    php scripts/memory.php sync-portfolio
```

Загружает тикеры из портфеля T-Invest и сохраняет в память.

**Шаг 6:** Показать синхронизированные позиции

```bash
    php scripts/memory.php positions
```

**Шаг 7:** Очистить память

```bash
    php scripts/memory.php clear
```

## Результат

Файл: `data/memory/user.json`

Поля:

| Поле                 | Описание                                       | Значения                            |
|----------------------|------------------------------------------------|-------------------------------------|
| risk_tolerance       | Толерантность к риску                          | conservative, moderate, aggressive  |
| horizon              | Инвестиционный горизонт                        | short-term, medium-term, long-term  |
| style                | Стиль инвестирования                           | value, growth, dividend, momentum   |
| favorite_sectors     | Избранные секторы                              | массив строк                        |
| avoid_sectors        | Секторы для исключения                         | массив строк                        |
| max_position_pct     | Максимальная доля одной позиции в портфеле     | число                               |
| positions            | Тикеры из портфеля (синхронизация)             | массив строк                        |
| positions_updated_at | Дата последней синхронизации портфеля          | ISO 8601                            |
| updated_at           | Дата последнего обновления                     | ISO 8601                            |

## Значения по умолчанию

```json
{
  "risk_tolerance": "moderate",
  "horizon": "long-term",
  "style": "value",
  "favorite_sectors": [],
  "avoid_sectors": [],
  "max_position_pct": 10,
  "positions": []
}
```

## Использование в анализе

**Перед анализом портфеля или рекомендациями:**

```bash
    php scripts/memory.php profile
    php scripts/memory.php positions
```

**Учёт профиля при рекомендациях:**

| Профиль          | Рекомендации                                    |
|------------------|-------------------------------------------------|
| conservative     | Дивидендные акции, облигации, низкая волатильность |
| moderate         | Баланс рост/дивиденды, средний риск             |
| aggressive       | Growth-акции, высокий риск/доходность           |

| Горизонт         | Подход                                         |
|------------------|------------------------------------------------|
| short-term       | Технический анализ, momentum                   |
| medium-term      | Фундаментал + теханка                          |
| long-term        | Фундаментальный анализ, дивиденды              |

| Стиль            | Фильтр                                         |
|------------------|------------------------------------------------|
| value            | P/E < 10, P/B < 1                              |
| growth           | Высокий рост выручки/прибыли                   |
| dividend         | Div Yield > 6%, стабильные выплаты             |
| momentum         | Тренд, RSI, объёмы                             |

**Ограничения:**

- `max_position_pct` — не рекомендовать увеличение позиции выше лимита
- `avoid_sectors` — исключить акции из нежелательных секторов
- `favorite_sectors` — приоритизировать при равных условиях
