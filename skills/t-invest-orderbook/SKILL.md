---
name: t-invest-orderbook
description: Получение стакана (order book) через T-Invest API
---

# T-Invest Orderbook

Получение стакана заявок (order book / glass) для анализа ликвидности и спреда.

## Когда использовать

- Анализ ликвидности инструмента
- Оценка спреда bid/ask
- Определение уровней поддержки и сопротивления
- Анализ дисбаланса спроса/предложения
- Перед входом в позицию для оценки глубины рынка

## Как использовать

**Паттерн сохранения результатов:**

```
data/{skill}/results/{YYYY-MM-DD}/{operation}-{YYYY-MM-DD}_{HH-II-SS}.{format}
```

### market:orderbook

Стакан заявок (order book) для анализа ликвидности, спреда и дисбаланса спроса/предложения. Используется перед входом в позицию для оценки глубины рынка.

```bash
mkdir -p data/t-invest-orderbook/results/2026-03-22
./vendor/bin/t-invest market:orderbook --ticker=SBER --format=json > data/t-invest-orderbook/results/2026-03-22/orderbook-sber-2026-03-22_14-30-00.json
```

```bash
./vendor/bin/t-invest market:orderbook [options]
```

Опции:

| Опция   | Сокращение | Описание                    | Значения            | По умолчанию |
|---------|------------|-----------------------------|---------------------|--------------|
| --ticker| -t         | Тикер инструмента           | SBER, GAZP, ...     | —            |
| --figi  |            | FIGI инструмента            | BBG...              | —            |
| --depth | -d         | Глубина стакана             | 1-50                | 20           |
| --format| -f         | Формат вывода               | md, json, csv, text | md           |

> Требуется `--ticker` или `--figi`. Если указаны оба, проверяется соответствие.

## Результат

Поля:

| Поле            | Описание                       |
|-----------------|--------------------------------|
| ASKS            | Заявки на продажу (красным)    |
| Price           | Цена заявки                    |
| Quantity        | Количество лотов               |
| BIDS            | Заявки на покупку (зелёным)    |
| Spread          | Разница между лучшим ask и bid |
| Bids/Asks count | Количество уровней             |

### Интерпретация

| Индикатор              | Значение                              |
|------------------------|---------------------------------------|
| Узкий спред (< 0.1%)   | Высокая ликвидность                   |
| Широкий спред (> 0.5%) | Низкая ликвидность                    |
| Дисбаланс bid/ask      | Давление покупателей/продавцов        |
| Стены (большие объёмы) | Уровни поддержки/сопротивления        |

## Типовые сценарии

### Быстрая оценка ликвидности

```bash
mkdir -p data/t-invest-orderbook/results/2026-03-22
./vendor/bin/t-invest market:orderbook -t SBER -d 5 --format=json > data/t-invest-orderbook/results/2026-03-22/orderbook-sber-5-2026-03-22_14-30-00.json
```

### Полный анализ стакана

```bash
mkdir -p data/t-invest-orderbook/results/2026-03-22
./vendor/bin/t-invest market:orderbook -t SBER -d 50 --format=json > data/t-invest-orderbook/results/2026-03-22/orderbook-sber-50-2026-03-22_14-30-00.json
```

### Поиск уровней (стены в стакане)

```bash
mkdir -p data/t-invest-orderbook/results/2026-03-22
./vendor/bin/t-invest market:orderbook -t GAZP -d 20 --format=json > data/t-invest-orderbook/results/2026-03-22/orderbook-gazp-20-2026-03-22_14-30-00.json
```
