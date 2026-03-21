---
name: moex
description: Работа с Московской Биржей через MOEX ISS API — спецификации, рыночные данные, индексы, итоги торгов
---

# MOEX

Навык для работы с Московской Биржей через MOEX ISS API.

## Когда использовать

- Получение спецификации инструмента (ISIN, уровень списка, тип)
- Рыночные данные (цены, объёмы, OHLC)
- Итоги торгов по рынкам (акции, РЕПО)
- Проверка вхождения в индексы МосБиржи
- Оценка ликвидности инструмента

## Ограничения

**MOEX ISS API отдаёт данные с задержкой ~20 минут.**

Для сиюминутной оценки рынка и торговых решений используйте T-Invest API (tinvest skill). MOEX подходит для:
- Исторического анализа
- Фундаментальных данных
- Спецификаций инструментов
- Проверки индексного состава

## Как использовать

**Шаг 1:** Спецификация инструмента

```bash
moex security:specification <ticker>
```

Параметры:

| Параметр | Описание          | По умолчанию |
|----------|-------------------|--------------|
| ticker   | Тикер инструмента | обязателен   |

Примеры:

```bash
moex security:specification SBER
moex security:specification GAZP
```

**Шаг 2:** Рыночные данные

```bash
moex security:trade-data <ticker>
```

Параметры:

| Параметр | Описание          | По умолчанию |
|----------|-------------------|--------------|
| ticker   | Тикер инструмента | обязателен   |

Примеры:

```bash
moex security:trade-data SBER
moex security:trade-data LKOH
```

**Шаг 3:** Итоги торгов

```bash
moex security:aggregates <ticker> [options]
```

Параметры:

| Параметр | Описание          | По умолчанию |
|----------|-------------------|--------------|
| ticker   | Тикер инструмента | обязателен   |

Опции:

| Опция    | Сокращение | Описание              | Значения        | По умолчанию |
|----------|------------|-----------------------|-----------------|--------------|
| --sort   | -s         | Сортировка            | volume, market  | volume       |
| --order  | -o         | Порядок сортировки    | asc, desc       | desc         |
| --limit  | -l         | Ограничить число строк| число           | 0 (все)      |
| --format | -f         | Формат вывода         | table, json     | table        |

Примеры:

```bash
moex security:aggregates SBER
moex security:aggregates GMKN
moex security:aggregates SBER --sort=volume --order=desc --limit=3
moex security:aggregates SBER --format=json
```

**Шаг 4:** Индексы

```bash
moex security:indices <ticker>
```

Параметры:

| Параметр | Описание          | По умолчанию |
|----------|-------------------|--------------|
| ticker   | Тикер инструмента | обязателен   |

Примеры:

```bash
moex security:indices SBER
moex security:indices ROSN
```

**Шаг 5:** Полный анализ бумаги

```bash
moex security:specification <ticker>
moex security:trade-data <ticker>
moex security:indices <ticker>
moex security:aggregates <ticker>
```

Примеры:

```bash
moex security:specification SBER
moex security:trade-data SBER
moex security:indices SBER
moex security:aggregates SBER
```

## Результат

### security:specification

Поля:

| Поле       | Описание                    |
|------------|-----------------------------|
| ISIN       | Международный идентификатор |
| List Level | Уровень списка (1, 2, 3)    |
| Type       | Тип инструмента             |
| Issue Size | Размер эмиссии              |

### security:trade-data

Поля:

| Поле         | Описание       |
|--------------|----------------|
| Last         | Последняя цена |
| Open         | Цена открытия  |
| High         | Максимум       |
| Low          | Минимум        |
| Volume Today | Объём за день  |

### security:aggregates

Поля:

| Поле   | Описание            |
|--------|---------------------|
| volume | Объём торгов        |
| market | Рынок (акции, РЕПО) |

### security:indices

Поля:

| Поле  | Описание                    |
|-------|-----------------------------|
| index | Название индекса            |
| from  | Дата включения              |
| till  | Дата исключения (если есть) |

## Сценарии

### Оценка ликвидности

```bash
moex security:aggregates SBER
```

| Объём в день | Ликвидность | Действие               |
|--------------|-------------|------------------------|
| > 3 млрд     | Высокая     | Можно торговать крупно |
| 1-3 млрд     | Средняя     | Лимитные заявки        |
| < 1 млрд     | Низкая      | Осторожно              |

### Проверка индексной значимости

```bash
moex security:indices SBER
```

- Входит в IMOEX → низкий риск ликвидности
- `Till` дата в прошлом → исключена из индекса

## Справочник

MOEX ISS API: https://iss.moex.com/iss/reference/
