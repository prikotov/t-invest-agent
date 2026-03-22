---
name: t-invest-candles
description: Получение исторических свечей (OHLCV) через T-Invest API
---

# T-Invest Candles

Получение исторических свечей для технического анализа.

## Когда использовать

- Технический анализ графиков
- Расчёт индикаторов (RSI, MACD, MA)
- Поиск паттернов и трендов
- Анализ объёмов

## Как использовать

**Паттерн сохранения результатов:**

```
data/{skill}/results/{YYYY-MM-DD}/{operation}-{YYYY-MM-DD}_{HH-II-SS}.{format}
```

```bash
mkdir -p data/t-invest-candles/results/2026-03-22
./vendor/bin/t-invest market:candles --ticker=SBER --format=json > data/t-invest-candles/results/2026-03-22/candles-sber-2026-03-22_14-30-00.json
```

```bash
./vendor/bin/t-invest market:candles [options]
```

Опции:

| Опция      | Сокращение | Описание                          | Значения           | По умолчанию |
|------------|------------|-----------------------------------|--------------------|--------------|
| --ticker   | -t         | Тикер инструмента (SBER, GAZP)    |                    |              |
| --figi     |            | FIGI инструмента                  | BBG...             |              |
| --from     | -f         | Начало периода                    | Y-m-d или relative | -7 days      |
| --to       |            | Конец периода                     | Y-m-d или relative | now          |
| --interval | -i         | Интервал свечи                    | см. ниже           | 1h           |
| --limit    | -l         | Макс. свечей                      | число              | 100          |
| --format   |            | Формат вывода                     | md, json, csv, text| md           |

> Требуется `--ticker` или `--figi`. Если указаны оба, проверяется соответствие.

### Интервалы

| Значение                      | Описание  |
|-------------------------------|-----------|
| 5s, 10s, 30s                  | Секундные |
| 1m, 2m, 3m, 5m, 10m, 15m, 30m | Минутные  |
| 1h, 2h, 4h                    | Часовые   |
| 1d, day                       | Дневной   |
| 1w, week                      | Недельный |
| 1M, month                     | Месячный  |

### Примеры

```bash
mkdir -p data/t-invest-candles/results/2026-03-22
./vendor/bin/t-invest market:candles --ticker=SBER --format=json > data/t-invest-candles/results/2026-03-22/candles-sber-2026-03-22_14-30-00.json
```

```bash
mkdir -p data/t-invest-candles/results/2026-03-22
./vendor/bin/t-invest market:candles -t SBER --from=2024-01-01 --to=2024-01-31 --format=json > data/t-invest-candles/results/2026-03-22/candles-sber-jan-2026-03-22_14-30-00.json
```

```bash
mkdir -p data/t-invest-candles/results/2026-03-22
./vendor/bin/t-invest market:candles -t GAZP -i 1d -l 30 --format=json > data/t-invest-candles/results/2026-03-22/candles-gazp-daily-2026-03-22_14-30-00.json
```

```bash
mkdir -p data/t-invest-candles/results/2026-03-22
./vendor/bin/t-invest market:candles -t LKOH -i 4h --from="-30 days" --format=json > data/t-invest-candles/results/2026-03-22/candles-lkoh-4h-2026-03-22_14-30-00.json
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
mkdir -p data/t-invest-candles/results/2026-03-22
./vendor/bin/t-invest market:candles -t SBER -i 1d -l 60 --format=json > data/t-invest-candles/results/2026-03-22/candles-sber-daily-2026-03-22_14-30-00.json
```

### Внутридневной анализ

```bash
mkdir -p data/t-invest-candles/results/2026-03-22
./vendor/bin/t-invest market:candles -t SBER -i 15m --from="-1 day" --format=json > data/t-invest-candles/results/2026-03-22/candles-sber-intraday-2026-03-22_14-30-00.json
```

### Расчёт MA50/MA200

```bash
mkdir -p data/t-invest-candles/results/2026-03-22
./vendor/bin/t-invest market:candles -t SBER -i 1d -l 200 --format=json > data/t-invest-candles/results/2026-03-22/candles-sber-ma200-2026-03-22_14-30-00.json
```
