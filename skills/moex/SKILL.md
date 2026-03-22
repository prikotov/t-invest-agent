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

| Опция    | Сокращение | Описание      | Значения            | По умолчанию |
|----------|------------|---------------|---------------------|--------------|
| --format | -f         | Формат вывода | table, json, csv, md | table        |

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

| Опция    | Сокращение | Описание      | Значения            | По умолчанию |
|----------|------------|---------------|---------------------|--------------|
| --format | -f         | Формат вывода | table, json, csv, md | table        |

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

| Опция    | Сокращение | Описание                | Значения                  | По умолчанию |
|----------|------------|-------------------------|---------------------------|--------------|
| --date   | -d         | Дата (YYYY-MM-DD)       | YYYY-MM-DD                | нет          |
| --sort   | -s         | Сортировка по полю      | value, volume, trades, date | date       |
| --order  | -o         | Порядок сортировки      | asc, desc                 | desc         |
| --limit  | -l         | Ограничить число строк  | число                     | 0 (все)      |
| --format | -f         | Формат вывода           | table, json, csv, md      | table        |

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

| Опция    | Сокращение | Описание      | Значения            | По умолчанию |
|----------|------------|---------------|---------------------|--------------|
| --format | -f         | Формат вывода | table, json, csv, md | table        |

## Результат

### security:specification

Поля:

| Поле       | Описание                    |
|------------|-----------------------------|
| Ticker     | Тикер инструмента           |
| Name       | Краткое название            |
| ISIN       | Международный идентификатор |
| RegNumber  | Регистрационный номер       |
| Type       | Тип инструмента             |
| Group      | Группа инструментов         |
| List       | Уровень списка (1, 2, 3)    |
| IssueDate  | Дата эмиссии                |
| FaceValue  | Номинал                     |
| Currency   | Валюта номинала             |
| IssueSize  | Размер эмиссии              |

### security:trade-data

Поля:

| Поле      | Описание                |
|-----------|-------------------------|
| Ticker    | Тикер инструмента       |
| ShortName | Краткое название        |
| Name      | Полное название         |
| Board     | Торговая площадка       |
| PrevPrice | Цена предыдущего закрытия |
| Open      | Цена открытия           |
| High      | Максимум дня            |
| Low       | Минимум дня             |
| Last      | Последняя цена          |
| Volume    | Объём торгов            |
| Time      | Время последней сделки  |

### security:aggregates

Поля:

| Поле    | Описание                      |
|---------|-------------------------------|
| Market  | Рынок (акции, РЕПО и т.д.)    |
| Date    | Дата торгов                   |
| Value   | Объём в деньгах               |
| Volume  | Объём в штуках                |
| Trades  | Количество сделок             |
| Updated | Время обновления              |

### security:indices

Поля:

| Поле    | Описание                      |
|---------|-------------------------------|
| Ticker  | Тикер индекса                 |
| Name    | Название индекса              |
| From    | Дата включения                |
| Till    | Дата исключения (если есть)   |
| Value   | Текущее значение индекса      |
| Change% | Изменение в %                 |
| Change  | Изменение в пунктах           |

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

