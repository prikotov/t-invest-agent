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

## Как использовать

```bash
./vendor/bin/moex schedule [--engine=stock] [--market=shares]
```

| Опция    | Описание                              | По умолчанию |
|----------|---------------------------------------|--------------|
| --engine | Движок (stock, currency, futures)     | stock        |
| --market | Рынок (shares, bonds, currency)       | —            |

## Вывод

```
MOEX Trading Schedule

STOCK / Акции
  Main session: 10:00 - 18:40
  Clearing: 18:40 - 18:50
  Evening session: 19:00 - 23:50

STOCK / Облигации
  Main session: 10:00 - 18:40
```

## Поля ответа

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
./vendor/bin/moex schedule --engine=stock --market=shares
```

### Все рынки

```bash
./vendor/bin/moex schedule
```

### Фьючерсы

```bash
./vendor/bin/moex schedule --engine=futures
```

## Результат

**{ENGINE} / {MARKET}**

- Main session: HH:MM - HH:MM
- Evening session: HH:MM - HH:MM (если есть)
- Clearing: HH:MM - HH:MM (если есть)

{ENGINE} {MARKET}
