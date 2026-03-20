---
name: memory
description: Память агента — хранение профиля инвестора и предпочтений пользователя
---

# Memory

## Когда использовать

- Сохранение предпочтений пользователя как инвестора
- Хранение профиля риска
- Запоминание избранных тикеров и секторов
- Учёт ограничений и предпочтений в рекомендациях

## Как использовать

**Шаг 1:** Показать профиль

```bash
    php bin/agent memory
```

Выводит текущий профиль пользователя или значения по умолчанию.

**Шаг 2:** Получить значение

```bash
    php bin/agent memory get <key>
```

Параметры:

| Параметр | Описание          |
|----------|-------------------|
| key      | Ключ памяти       |

Примеры:

```bash
    php bin/agent memory get risk_tolerance
    php bin/agent memory get favorite_sectors
```

**Шаг 3:** Установить значение

```bash
    php bin/agent memory set <key> <value>
```

Параметры:

| Параметр | Описание                     |
|----------|------------------------------|
| key      | Ключ памяти                  |
| value    | Значение (JSON для массивов) |

Примеры:

```bash
    php bin/agent memory set risk_tolerance moderate
    php bin/agent memory set horizon long-term
    php bin/agent memory set max_position_pct 15
    php bin/agent memory set favorite_sectors '["financial","energy"]'
```

**Шаг 4:** Обновить профиль

```bash
    php bin/agent memory update [опции]
```

Опции:

| Опция     | Описание                | Значения                           | По умолчанию |
|-----------|-------------------------|------------------------------------|--------------|
| --risk    | Толерантность к риску   | conservative, moderate, aggressive | moderate     |
| --horizon | Горизонт инвестирования | short-term, medium-term, long-term | long-term    |
| --style   | Стиль инвестирования    | value, growth, dividend, momentum  | value        |
| --sectors | Избранные секторы       | через запятую                      | (пусто)      |
| --max-pos | Макс. доля позиции      | число в %                          | 10           |

Примеры:

```bash
    php bin/agent memory update --risk=aggressive --horizon=short-term
    php bin/agent memory update --style=dividend --sectors=financial,energy --max-pos=15
```

**Шаг 5:** Очистить память

```bash
    php bin/agent memory clear
```

## Результат

Файл: `data/memory/user.json`

Поля:

| Поле             | Описание                                   | Значения                           |
|------------------|--------------------------------------------|------------------------------------|
| risk_tolerance   | Толерантность к риску                      | conservative, moderate, aggressive |
| horizon          | Инвестиционный горизонт                    | short-term, medium-term, long-term |
| style            | Стиль инвестирования                       | value, growth, dividend, momentum  |
| favorite_sectors | Избранные секторы                          | массив строк                       |
| avoid_sectors    | Секторы для исключения                     | массив строк                       |
| max_position_pct | Максимальная доля одной позиции в портфеле | число                              |
| updated_at       | Дата последнего обновления                 | ISO 8601                           |

## Использование в анализе

Перед анализом портфеля или рекомендациями:

```bash
    php bin/agent memory
```

Учёт профиля при рекомендациях:

| Профиль      | Рекомендации                                       |
|--------------|----------------------------------------------------|
| conservative | Дивидендные акции, облигации, низкая волатильность |
| moderate     | Баланс рост/дивиденды, средний риск                |
| aggressive   | Growth-акции, высокий риск/доходность              |

| Горизонт    | Подход                             |
|-------------|------------------------------------|
| short-term  | Технический анализ, momentum       |
| medium-term | Фундаментал + теханка              |
| long-term   | Фундаментальный анализ, дивиденды  |

| Стиль    | Фильтр                             |
|----------|------------------------------------|
| value    | P/E < 10, P/B < 1                  |
| growth   | Высокий рост выручки/прибыли       |
| dividend | Div Yield > 6%, стабильные выплаты |
| momentum | Тренд, RSI, объёмы                 |

Ограничения:

- `max_position_pct` — не рекомендовать увеличение позиции выше лимита
- `avoid_sectors` — исключить акции из нежелательных секторов
- `favorite_sectors` — приоритизировать при равных условиях
