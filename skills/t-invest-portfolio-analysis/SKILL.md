---
name: t-invest-portfolio-analysis
description: Анализ структуры и метрик инвестиционного портфеля T-Invest.
---

# Portfolio Analysis

## Когда использовать

- Оценка текущих позиций
- Анализ распределения активов
- Диагностика портфеля

## Как использовать

**Паттерн сохранения результатов:**

```
data/{skill}/results/{YYYY-MM-DD}/{operation}-{YYYY-MM-DD}_{HH-II-SS}.{format}
```

**Шаг 1:** Получить позиции

```bash
mkdir -p data/t-invest-portfolio-analysis/results/2026-03-22
./vendor/bin/t-invest portfolio:show --format=json > data/t-invest-portfolio-analysis/results/2026-03-22/portfolio-2026-03-22_14-30-00.json
```

`t-invest portfolio:show`

Опции:

| Опция        | Сокращение | Описание                  | Значения                   | По умолчанию |
|--------------|------------|---------------------------|----------------------------|--------------|
| --instrument | -i         | Фильтр по тикеру или FIGI | SBER, BBG004730N88         | все          |
| --sort       | -s         | Сортировка                | ticker, yield, value, type | ticker       |
| --order      | -o         | Порядок сортировки        | asc, desc                  | asc          |
| --limit      | -l         | Ограничить число позиций  | число                      | 0 (все)      |
| --format     | -f         | Формат вывода             | md, json, csv, text        | md           |

Поля:

| Поле      | Описание          |
|-----------|-------------------|
| ticker    | Тикер инструмента |
| type      | Тип инструмента   |
| quantity  | Количество        |
| avg_price | Средняя цена      |
| yield     | Прибыль/убыток    |
| price     | Текущая цена      |

**Шаг 2:** Для каждой позиции > 5% веса — анализ

```bash
mkdir -p data/t-invest-portfolio-analysis/results/2026-03-22
./vendor/bin/moex security:trade-data SBER --format=json > data/t-invest-portfolio-analysis/results/2026-03-22/trade-data-sber-2026-03-22_14-30-00.json
./vendor/bin/news news:search "SBER" --format=json > data/t-invest-portfolio-analysis/results/2026-03-22/news-sber-2026-03-22_14-30-00.json
./vendor/bin/t-invest instruments:fundamentals SBER --format=json > data/t-invest-portfolio-analysis/results/2026-03-22/fundamentals-sber-2026-03-22_14-30-00.json
```

`moex security:trade-data`

| Аргумент | Описание                        |
|----------|---------------------------------|
| ticker   | Тикер инструмента (позиционный) |

Опции:

| Опция    | Описание       | Значения            | По умолчанию |
|----------|----------------|---------------------|--------------|
| --format | Формат вывода  | md, json, csv, text | md           |

Поля: `last`, `open`, `high`, `low`, `volume`

`news news:search`

| Аргумент | Описание         |
|----------|------------------|
| query    | Поисковый термин |

Опции:

| Опция     | Сокращение | Описание            | Значения            | По умолчанию |
|-----------|------------|---------------------|---------------------|--------------|
| --source  | -s         | Фильтр по источнику | interfax, tass, ... | все          |
| --category|            | Фильтр по категории | Экономика, Финансы  | все          |
| --days    | -d         | За последние N дней | число               | 7            |
| --limit   | -l         | Лимит записей       | число               | 50           |
| --format  | -f         | Формат вывода       | md, json, csv, text | md           |
| --no-fetch|            | Только поиск в кэше | флаг                | выкл         |

`t-invest instruments:fundamentals`

| Аргумент | Описание                           |
|----------|------------------------------------|
| tickers  | Тикеры (позиционные, через пробел) |

Опции:

| Опция    | Описание       | Значения            | По умолчанию |
|----------|----------------|---------------------|--------------|
| --format | Формат вывода  | md, json, csv, text | md           |

Поля: `pe`, `pb`, `roe`, `dividend_yield`

**Шаг 3:** Сформировать сводку с рекомендациями

## Результат

**Портфель: Сводка**

| Поле         | Описание                 |
|--------------|--------------------------|
| Тикер        | Тикер инструмента        |
| Вес          | Доля в портфеле          |
| Цена         | Текущая цена             |
| Изм          | Изменение за период      |
| Рекомендация | HOLD / BUY MORE / REDUCE |

**Важные новости:**

- {TICKER}: [новость]

**Фундаментальная картина:**

- {TICKER}: P/E, P/B, дивиденды

Не является инвестиционной рекомендацией. {TICKERS}
