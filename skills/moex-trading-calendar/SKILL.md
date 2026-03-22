---
name: moex-trading-calendar
description: Торговый календарь через MOEX ISS API.
---

# Торговый календарь (MOEX)

Получение расписания торгов через MOEX ISS API.

## Когда использовать

- Получение расписания без токена T-Invest
- Проверка часов торгов для конкретного рынка
- Определение времени клиринга и вечерней сессии

## Ограничения

**MOEX ISS API отдаёт данные с задержкой ~20 минут.**

Торговый календарь не зависит от задержки — расписание статично. Для остальных данных используйте T-Invest API.

## Как использовать

**Паттерн сохранения результатов:**

```
data/{skill}/results/{YYYY-MM-DD}/{operation}-{YYYY-MM-DD}_{HH-II-SS}.{format}
```

### schedule

Расписание торговых сессий по рынкам: часы торгов, клиринг, вечерняя сессия. Используется для планирования сделок и проверки доступности рынка.

```bash
mkdir -p data/moex-trading-calendar/results/2026-03-22
./vendor/bin/moex schedule --format=json > data/moex-trading-calendar/results/2026-03-22/schedule-2026-03-22_14-30-00.json
```

```bash
./vendor/bin/moex schedule [options]
```

Опции:

| Опция    | Сокращение | Описание                          | Значения                   | По умолчанию |
|----------|------------|-----------------------------------|----------------------------|--------------|
| --engine |            | Торговый движок                   | stock, currency, futures   | stock        |
| --market |            | Рынок                             | shares, bonds, currency    | —            |
| --format |            | Формат вывода                     | md, json, csv, text        | md           |

## Результат

Поля:

| Поле          | Описание                    |
|---------------|-----------------------------|
| engine        | Торговый движок             |
| market        | Рынок                       |
| title         | Название рынка (рус.)       |
| startTime     | Начало основной сессии      |
| endTime       | Конец основной сессии       |
| auctionStart  | Аукцион открытия            |
| auctionEnd    | Аукцион закрытия            |
| eveningStart  | Начало вечерней сессии      |
| eveningEnd    | Конец вечерней сессии       |
| clearingStart | Начало клиринга             |
| clearingEnd   | Конец клиринга              |

## Типовые сценарии

### Расписание рынка акций

```bash
mkdir -p data/moex-trading-calendar/results/2026-03-22
./vendor/bin/moex schedule --engine=stock --market=shares --format=json > data/moex-trading-calendar/results/2026-03-22/schedule-shares-2026-03-22_14-30-00.json
```

### Все рынки

```bash
mkdir -p data/moex-trading-calendar/results/2026-03-22
./vendor/bin/moex schedule --format=json > data/moex-trading-calendar/results/2026-03-22/schedule-2026-03-22_14-30-00.json
```

### Фьючерсы

```bash
mkdir -p data/moex-trading-calendar/results/2026-03-22
./vendor/bin/moex schedule --engine=futures --format=json > data/moex-trading-calendar/results/2026-03-22/schedule-futures-2026-03-22_14-30-00.json
```
