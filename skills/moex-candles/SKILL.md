---
name: moex-candles
description: Получение исторических свечей (OHLCV) через MOEX ISS API
---

# MOEX Candles

Получение исторических свечей для технического анализа.

## Когда использовать

- Технический анализ графиков
- Расчёт индикаторов (RSI, MACD, MA)
- Поиск паттернов и трендов
- Анализ объёмов

## Ограничения

**MOEX ISS API отдаёт данные с задержкой ~20 минут.**

Для сиюминутной оценки рынка и торговых решений используйте T-Invest API (t-invest-candles skill). MOEX подходит для исторического анализа.

## Как использовать

**Паттерн сохранения результатов:**

```
data/{skill}/results/{YYYY-MM-DD}/{operation}-{YYYY-MM-DD}_{HH-II-SS}.{format}
```

```bash
mkdir -p data/moex-candles/results/2026-03-22
./vendor/bin/moex security:candles SBER --format=json > data/moex-candles/results/2026-03-22/candles-sber-2026-03-22_14-30-00.json
```

```bash
./vendor/bin/moex security:candles <ticker> [options]
```

Аргументы:

| Аргумент | Описание                             |
|----------|--------------------------------------|
| ticker   | Тикер инструмента (SBER, GAZP, etc.) |

Опции:

| Опция      | Сокращение | Описание                | Значения             | По умолчанию |
|------------|------------|-------------------------|----------------------|--------------|
| --from     | -f         | Начало периода          | Y-m-d                | нет          |
| --to       | -t         | Конец периода           | Y-m-d                | нет          |
| --interval | -i         | Интервал свечи (минуты) | 1, 10, 60, 24, 7, 31 | 60           |
| --limit    | -l         | Макс. свечей            | число                | 100          |
| --format   |            | Формат вывода           | md, json, csv, text  | md           |

### Интервалы

| Значение | Описание |
|----------|----------|
| 1        | 1 минута |
| 10       | 10 минут |
| 60       | 1 час    |
| 24       | 1 день   |
| 7        | 1 неделя |
| 31       | 1 месяц  |

### Примеры

```bash
mkdir -p data/moex-candles/results/2026-03-22
./vendor/bin/moex security:candles SBER --format=json > data/moex-candles/results/2026-03-22/candles-sber-2026-03-22_14-30-00.json
```

```bash
mkdir -p data/moex-candles/results/2026-03-22
./vendor/bin/moex security:candles SBER --from=2024-01-01 --to=2024-01-31 --format=json > data/moex-candles/results/2026-03-22/candles-sber-jan-2026-03-22_14-30-00.json
```

```bash
mkdir -p data/moex-candles/results/2026-03-22
./vendor/bin/moex security:candles GAZP -i 24 -l 30 --format=json > data/moex-candles/results/2026-03-22/candles-gazp-daily-2026-03-22_14-30-00.json
```

## Результат

Вывод в консоль:

| Поле   | Описание             |
|--------|----------------------|
| Time   | Время открытия свечи |
| Open   | Цена открытия        |
| High   | Максимальная цена    |
| Low    | Минимальная цена     |
| Close  | Цена закрытия        |
| Volume | Объём                |

## Типовые сценарии

### Анализ тренда (дневные свечи)

```bash
mkdir -p data/moex-candles/results/2026-03-22
./vendor/bin/moex security:candles SBER -i 24 -l 60 --format=json > data/moex-candles/results/2026-03-22/candles-sber-daily-2026-03-22_14-30-00.json
```

### Внутридневной анализ

```bash
mkdir -p data/moex-candles/results/2026-03-22
./vendor/bin/moex security:candles SBER -i 10 --from=2024-03-15 --format=json > data/moex-candles/results/2026-03-22/candles-sber-intraday-2026-03-22_14-30-00.json
```

### Расчёт MA50/MA200

```bash
mkdir -p data/moex-candles/results/2026-03-22
./vendor/bin/moex security:candles SBER -i 24 -l 200 --format=json > data/moex-candles/results/2026-03-22/candles-sber-ma200-2026-03-22_14-30-00.json
```
