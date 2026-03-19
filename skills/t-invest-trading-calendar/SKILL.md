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

```bash
./vendor/bin/t-invest schedule [--exchange=MOEX] [--date=YYYY-MM-DD] [--days=7]
```

| Опция      | Описание                    | По умолчанию |
|------------|-----------------------------|--------------|
| --exchange | Код биржи (MOEX)            | MOEX         |
| --date     | Дата начала (YYYY-MM-DD)    | сегодня      |
| --days     | Количество дней             | 7            |

## Вывод

```
Trading Schedule: MOEX

2024-03-18 (Mon): Trading Day
  Morning auction: 09:50 - 10:00
  Main session: 10:00 - 18:40
  Clearing: 18:40 - 18:50
  Evening session: 19:00 - 23:50

2024-03-19 (Tue): Trading Day
  ...

2024-03-23 (Sat): NON-TRADING DAY
```

## Поля ответа

| Поле               | Описание                    |
|--------------------|-----------------------------|
| is_trading_day     | Торговый день / выходной    |
| start_time         | Начало основной сессии      |
| end_time           | Конец основной сессии       |
| morning_session    | Аукцион открытия            |
| evening_session    | Вечерняя сессия             |
| clearing           | Время клиринга              |
| holiday_name       | Название праздника (если есть) |

## Типовые сценарии

### Проверка "сегодня торги?"

```bash
./vendor/bin/t-invest schedule --date=$(date +%Y-%m-%d) --days=1
```

- `is_trading_day=false` → не торговать
- Проверить часы торгов перед выставлением заявок

### Планирование на неделю

```bash
./vendor/bin/t-invest schedule --days=7
```

- Определить ближайшие торговые дни
- Учесть праздники

## Результат

**{DATE}: [TRADING / NON-TRADING]**

- Основная сессия: HH:MM - HH:MM
- Вечерняя сессия: HH:MM - HH:MM (если есть)
- Клиринг: HH:MM - HH:MM (если есть)

{EXCHANGE}
