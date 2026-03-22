---
name: tinvest
description: Работа с портфелем через T-Invest API — позиции, цены, операции
---

# T-Invest Skill

Навык для работы с портфелем через T-Invest API.

## Паттерн сохранения результатов

```
data/{skill}/results/{YYYY-MM-DD}/{operation}-{YYYY-MM-DD}_{HH-II-SS}.{format}
```

## Команды

### portfolio:show

Позиции портфеля: тикеры, количество, средняя цена, текущая цена, доходность. Используется для обзора портфеля и мониторинга позиций.

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest portfolio:show --format=json > data/tinvest/results/2026-03-22/portfolio-2026-03-22_14-30-00.json
```

Опции:

| Опция        | Сокращение | Описание                  | Значения                   | По умолчанию |
|--------------|------------|---------------------------|----------------------------|--------------|
| --instrument | -i         | Фильтр по тикеру или FIGI | SBER, BBG004730N88         | все          |
| --sort       | -s         | Сортировка                | ticker, yield, value, type | ticker       |
| --order      | -o         | Порядок сортировки        | asc, desc                  | asc          |
| --limit      | -l         | Ограничить число позиций  | число                      | 0 (все)      |
| --format     | -f         | Формат вывода             | md, json, csv, text        | md           |

Возвращает: позиции, количество, средняя цена, текущая цена, доходность (%).

### accounts:list

Список счетов с ID и статусом. Используется для выбора счёта при работе с API.

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest accounts:list --format=json > data/tinvest/results/2026-03-22/accounts-2026-03-22_14-30-00.json
```

### market:prices

Текущие рыночные цены по списку тикеров. Используется для быстрого получения актуальных цен.

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest market:prices SBER GAZP LKOH --format=json > data/tinvest/results/2026-03-22/prices-2026-03-22_14-30-00.json
```

### market:candles

Исторические свечи OHLCV. Используется для технического анализа и расчёта индикаторов.

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest market:candles --ticker=SBER --from="-7 days" --format=json > data/tinvest/results/2026-03-22/candles-sber-2026-03-22_14-30-00.json
```

### market:orderbook

Стакан заявок для анализа ликвидности и спреда.

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest market:orderbook SBER --depth=20 --format=json > data/tinvest/results/2026-03-22/orderbook-sber-2026-03-22_14-30-00.json
```

### instruments:fundamentals

Фундаментальные метрики: P/E, P/B, ROE, дивидендная доходность. Используется для оценки инвестиционной привлекательности.

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest instruments:fundamentals SBER GAZP --format=json > data/tinvest/results/2026-03-22/fundamentals-2026-03-22_14-30-00.json
```

### operations:history

История операций за период. Используется для анализа сделок и налогового учёта.

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest operations:history --from=2024-01-01 --to=2024-01-31 --format=json > data/tinvest/results/2026-03-22/operations-2026-03-22_14-30-00.json
```

### instruments:resolve

Резолв FIGI в тикер и обратно: FIGI, тикер, UID, ISIN, имя, classCode, currency, lot.

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest instruments:resolve BBG004730N88 --format=json > data/tinvest/results/2026-03-22/resolve-2026-03-22_14-30-00.json
```

## Типовые сценарии

### Еженедельный мониторинг

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest portfolio:show --format=json > data/tinvest/results/2026-03-22/portfolio-2026-03-22_14-30-00.json
```

Проверить: доходность позиций, отклонения > 5%

### Анализ кандидата для покупки

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest instruments:fundamentals GAZP --format=json > data/tinvest/results/2026-03-22/fundamentals-gazp-2026-03-22_14-30-00.json
./vendor/bin/t-invest market:candles --ticker=GAZP --from=2024-01-01 --format=json > data/tinvest/results/2026-03-22/candles-gazp-2026-03-22_14-30-00.json
```

### Топ позиций по доходности

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest portfolio:show --sort=yield --order=desc --limit=5 --format=json > data/tinvest/results/2026-03-22/portfolio-top-yield-2026-03-22_14-30-00.json
```

### Топ операций по сумме

```bash
mkdir -p data/tinvest/results/2026-03-22
./vendor/bin/t-invest operations:history --sort=payment --order=desc --limit=10 --format=json > data/tinvest/results/2026-03-22/operations-top-2026-03-22_14-30-00.json
```

## Справочник API

OpenAPI: https://russianinvestments.github.io/investAPI/swagger-ui/openapi.yaml
