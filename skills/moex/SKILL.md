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

**Паттерн сохранения результатов:**

```
data/{skill}/results/{YYYY-MM-DD}/{operation}-{YYYY-MM-DD}_{HH-II-SS}.{format}
```

### security:specification

```bash
mkdir -p data/moex/results/2026-03-22
./vendor/bin/moex security:specification SBER --format=json > data/moex/results/2026-03-22/spec-sber-2026-03-22_14-30-00.json
```

```bash
./vendor/bin/moex security:specification <ticker> [options]
```

Аргументы:

| Аргумент | Описание                   |
|----------|----------------------------|
| ticker   | Тикер инструмента (SBER)   |

Опции:

| Опция    | Описание       | Значения            | По умолчанию |
|----------|----------------|---------------------|--------------|
| --format | Формат вывода  | md, json, csv, text | md           |

### security:trade-data

```bash
mkdir -p data/moex/results/2026-03-22
./vendor/bin/moex security:trade-data SBER --format=json > data/moex/results/2026-03-22/trade-data-sber-2026-03-22_14-30-00.json
```

```bash
./vendor/bin/moex security:trade-data <ticker> [options]
```

Аргументы:

| Аргумент | Описание                   |
|----------|----------------------------|
| ticker   | Тикер инструмента (SBER)   |

Опции:

| Опция    | Описание       | Значения            | По умолчанию |
|----------|----------------|---------------------|--------------|
| --format | Формат вывода  | md, json, csv, text | md           |

### security:aggregates

```bash
mkdir -p data/moex/results/2026-03-22
./vendor/bin/moex security:aggregates SBER --format=json > data/moex/results/2026-03-22/aggregates-sber-2026-03-22_14-30-00.json
```

```bash
./vendor/bin/moex security:aggregates <ticker> [options]
```

Аргументы:

| Аргумент | Описание                   |
|----------|----------------------------|
| ticker   | Тикер инструмента (SBER)   |

Опции:

| Опция    | Сокращение | Описание               | Значения            | По умолчанию |
|----------|------------|------------------------|---------------------|--------------|
| --sort   | -s         | Сортировка             | volume, market       | volume       |
| --order  | -o         | Порядок сортировки     | asc, desc           | desc         |
| --limit  | -l         | Ограничить число строк | число               | 0 (все)      |
| --format |            | Формат вывода          | md, json, csv, text | md           |

### security:indices

```bash
mkdir -p data/moex/results/2026-03-22
./vendor/bin/moex security:indices SBER --format=json > data/moex/results/2026-03-22/indices-sber-2026-03-22_14-30-00.json
```

```bash
./vendor/bin/moex security:indices <ticker> [options]
```

Аргументы:

| Аргумент | Описание                   |
|----------|----------------------------|
| ticker   | Тикер инструмента (SBER)   |

Опции:

| Опция    | Описание       | Значения            | По умолчанию |
|----------|----------------|---------------------|--------------|
| --format | Формат вывода  | md, json, csv, text | md           |

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

## Типовые сценарии

### Оценка ликвидности

```bash
mkdir -p data/moex/results/2026-03-22
./vendor/bin/moex security:aggregates SBER --format=json > data/moex/results/2026-03-22/aggregates-sber-2026-03-22_14-30-00.json
```

| Объём в день | Ликвидность | Действие               |
|--------------|-------------|------------------------|
| > 3 млрд     | Высокая     | Можно торговать крупно |
| 1-3 млрд     | Средняя     | Лимитные заявки        |
| < 1 млрд     | Низкая      | Осторожно              |

### Проверка индексной значимости

```bash
mkdir -p data/moex/results/2026-03-22
./vendor/bin/moex security:indices SBER --format=json > data/moex/results/2026-03-22/indices-sber-2026-03-22_14-30-00.json
```

- Входит в IMOEX → низкий риск ликвидности
- `till` дата в прошлом → исключена из индекса

### Полный анализ бумаги

```bash
mkdir -p data/moex/results/2026-03-22
./vendor/bin/moex security:specification SBER --format=json > data/moex/results/2026-03-22/spec-sber-2026-03-22_14-30-00.json
./vendor/bin/moex security:trade-data SBER --format=json > data/moex/results/2026-03-22/trade-data-sber-2026-03-22_14-30-00.json
./vendor/bin/moex security:indices SBER --format=json > data/moex/results/2026-03-22/indices-sber-2026-03-22_14-30-00.json
./vendor/bin/moex security:aggregates SBER --format=json > data/moex/results/2026-03-22/aggregates-sber-2026-03-22_14-30-00.json
```

