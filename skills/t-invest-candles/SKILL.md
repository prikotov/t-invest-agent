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

```bash
t-invest market:candles [options]
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
t-invest market:candles --ticker=SBER
t-invest market:candles -t SBER --from=2024-01-01 --to=2024-01-31
t-invest market:candles -t GAZP -i 1d -l 30
t-invest market:candles -t LKOH -i 4h --from="-30 days"
t-invest market:candles --figi=BBG004730N88 -i 1d -l 10
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
t-invest market:candles -t SBER -i 1d -l 60
```

### Внутридневной анализ
```bash
t-invest market:candles -t SBER -i 15m --from="-1 day"
```

### Расчёт MA50/MA200
```bash
t-invest market:candles -t SBER -i 1d -l 200
```
