---
name: t-invest-trading-calendar
description: Торговый календарь через T-Invest API.
---

# Торговый календарь (T-Invest)

Получение расписания торгов через T-Invest API.

## Когда использовать

- Проверка торгового дня перед выставлением заявки
- Определение часов торгов для конкретной биржи
- Учёт праздников и выходных

## Как использовать

**Паттерн сохранения результатов:**

```
data/{skill}/results/{YYYY-MM-DD}/{operation}-{YYYY-MM-DD}_{HH-II-SS}.{format}
```

### schedule

Торговый календарь: рабочие/выходные дни, часы торгов, праздники. Используется для проверки доступности рынка перед сделкой.

```bash
mkdir -p data/t-invest-trading-calendar/results/2026-03-22
./vendor/bin/t-invest schedule --format=json > data/t-invest-trading-calendar/results/2026-03-22/schedule-2026-03-22_14-30-00.json
```

```bash
./vendor/bin/t-invest schedule [options]
```

Опции:

| Опция      | Сокращение | Описание                    | Значения            | По умолчанию |
|------------|------------|-----------------------------|---------------------|--------------|
| --exchange |            | Код биржи                   | MOEX                | MOEX         |
| --date     |            | Дата начала (YYYY-MM-DD)    | 2026-03-22          | сегодня      |
| --days     |            | Количество дней             | 1-365               | 7            |
| --format   |            | Формат вывода               | md, json, csv, text | md           |

## Результат

Поля:

| Поле            | Описание                       |
|-----------------|--------------------------------|
| date            | Дата                           |
| is_trading_day  | Торговый день / выходной       |
| start_time      | Начало основной сессии         |
| end_time        | Конец основной сессии          |
| morning_session | Аукцион открытия               |
| evening_session | Вечерняя сессия                |
| clearing        | Время клиринга                 |
| holiday_name    | Название праздника (если есть) |

## Типовые сценарии

### Проверка "сегодня торги?"

```bash
mkdir -p data/t-invest-trading-calendar/results/2026-03-22
./vendor/bin/t-invest schedule --date=$(date +%Y-%m-%d) --days=1 --format=json > data/t-invest-trading-calendar/results/2026-03-22/schedule-today-2026-03-22_14-30-00.json
```

- `is_trading_day=false` → не торговать
- Проверить часы торгов перед выставлением заявок

### Планирование на неделю

```bash
mkdir -p data/t-invest-trading-calendar/results/2026-03-22
./vendor/bin/t-invest schedule --days=7 --format=json > data/t-invest-trading-calendar/results/2026-03-22/schedule-week-2026-03-22_14-30-00.json
```

- Определить ближайшие торговые дни
- Учесть праздники
