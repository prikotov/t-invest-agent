# Новые Skills для T-Invest Agent

Описание навыков, которые нужно реализовать на основе анализа Stonki AI.

---

## 1. recipe — Торговые рецепты

### Назначение
Живые торговые журналы для отслеживания идей от тезиса до результата.

### Команды

```bash
# Создание рецепта
recipe create --ticker=SBER \
  --thesis="Дивидендный доход 12%+" \
  --entry=250-260 \
  --target=300 \
  --stop=240

# Интерактивное создание
recipe create --interactive

# Список рецептов
recipe list [--status=active|closed|all]
recipe list --ticker=SBER

# Обновление (добавляет контент, не перезаписывает!)
recipe update --id=UUID --note="Цена достигла уровня входа"
recipe update --id=UUID --status=filled
recipe update --id=UUID --partial-fill --price=255 --quantity=100

# Точечное редактирование (аналог edit_recipe_content)
recipe edit --id=UUID --find="Зона: 250-260" --replace="Зона: 248-258"
recipe edit --id=UUID --find="Цель: 300" --replace="Цель: 320"

# Закрытие
recipe close --id=UUID --result=+15% --note="Цель достигнута"
recipe close --id=UUID --result=-5% --note="Сработал стоп"

# История
recipe history --ticker=SBER
recipe stats --period=month  # Статистика по закрытым

# Детали
recipe show --id=UUID
```

### Структура данных

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440000",
  "ticker": "SBER",
  "created_at": "2025-03-18T10:00:00Z",
  "status": "active",
  "thesis": {
    "summary": "Дивидендный доход 12%+",
    "rationale": "Исторически стабильные дивиденды, низкий P/E"
  },
  "entry": {
    "zone": [250, 260],
    "type": "limit",
    "filled": false,
    "fills": []
  },
  "target": {
    "price": 300,
    "rationale": "Сопротивление + 15% потенциал"
  },
  "stop": {
    "price": 240,
    "type": "hard",
    "rationale": "Поддержка"
  },
  "risk_reward": 2.5,
  "position": {
    "recommended_pct": 5,
    "actual_quantity": null
  },
  "updates": [
    {
      "date": "2025-03-19",
      "type": "note",
      "content": "Цена 255, хороший уровень для входа"
    }
  ],
  "result": null,
  "closed_at": null
}
```

### Интеграция

```bash
# Автоматический расчёт R:R при создании
recipe create --ticker=SBER --entry=250 --target=300 --stop=240
# Автоматически рассчитает R:R = 2.5

# Автоматическое получение текущей цены
recipe create --ticker=SBER
# Запросит текущую цену через moex security:trade-data
```

---

---

## 2. monitor — Мониторинг и алерты

### Назначение
Система отслеживания ценовых уровней, технических сигналов и запланированных проверок.

### Шаблоны мониторинга (из Stonki AI)

| Stonki Template | T-Invest Agent | Параметры | Приоритет |
|-----------------|----------------|-----------|-----------|
| **price_cross** | `monitor create price` | ticker, level, direction | HIGH |
| **iv_cross** | `monitor create signal --indicator=IV` | ticker, iv_threshold | LOW |
| **recurring_prompt** | `monitor create schedule` | cron, prompt | HIGH |

### Когда использовать какой шаблон

| Запрос пользователя | Команда | Шаблон |
|---------------------|---------|--------|
| "Сообщи когда SBER будет 260" | `monitor create price --ticker=SBER --level=260` | price_cross |
| "Следи когда SBER упадёт до 240" | `monitor create price --ticker=SBER --level=240 --direction=below` | price_cross |
| "Каждое утро проверяй портфель" | `monitor create schedule --cron="0 9 * * 1-5" --prompt="..."` | recurring_prompt |
| "Каждую пятницу отчёт" | `monitor create schedule --cron="0 18 * * 5" --prompt="..."` | recurring_prompt |
| "Алерт если RSI > 70" | `monitor create signal --ticker=SBER --indicator=RSI --condition=">70"` | signal |
| "Следи за волатильностью" | `monitor create signal --ticker=SBER --indicator=IV --condition=">0.4"` | iv_cross |

### Команды

```bash
# Ценовой алерт (price_cross)
monitor create price --ticker=SBER --level=260 --direction=above
monitor create price --ticker=SBER --level=240 --direction=below
monitor create price --ticker=SBER --range=240,260  # Оба уровня

# Технический алерт (signal)
monitor create signal --ticker=SBER --indicator=RSI --condition=">70"
monitor create signal --ticker=SBER --indicator=RSI --condition="<30"
monitor create signal --ticker=SBER --indicator=MACD --condition="crossover"
monitor create signal --ticker=SBER --indicator=IV --condition=">0.4"  # iv_cross

# Запланированная проверка (recurring_prompt)
monitor create schedule --cron="0 9 * * 1-5" \
  --prompt="Ежеутренняя проверка портфеля"
monitor create schedule --cron="0 18 * * 5" \
  --prompt="Еженедельный отчёт"

# Управление (pause/resume/update/delete)
monitor list [--type=price|signal|schedule]
monitor update --id=UUID --level=265
monitor pause --id=UUID
monitor resume --id=UUID
monitor delete --id=UUID

# Запуск демона (для непрерывного мониторинга)
monitor daemon start
monitor daemon stop
monitor daemon status
```

### Структура данных

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440001",
  "type": "price",
  "ticker": "SBER",
  "created_at": "2025-03-18T10:00:00Z",
  "status": "active",
  "condition": {
    "level": 260,
    "direction": "above"
  },
  "triggered": false,
  "triggered_at": null,
  "notifications": {
    "telegram": true,
    "email": false
  }
}
```

### Структура данных

```json
{
  "id": "550e8400-e29b-41d4-a716-446655440001",
  "type": "price",
  "ticker": "SBER",
  "created_at": "2025-03-18T10:00:00Z",
  "status": "active",
  "condition": {
    "level": 260,
    "direction": "above"
  },
  "triggered": false,
  "triggered_at": null,
  "notifications": {
    "telegram": true,
    "email": false
  }
}
```
}
```

### Типы мониторинга

```yaml
# price_cross
type: price
ticker: SBER
condition:
  level: 260
  direction: above  # или below, или both
action:
  notify: true
  create_recipe_update: true

# technical_signal
type: signal
ticker: SBER
condition:
  indicator: RSI
  period: 14
  operator: ">"
  value: 70
action:
  notify: true

# recurring_prompt
type: schedule
schedule: "0 9 * * 1-5"  # cron
prompt: |
  Проверить портфель:
  1. portfolio:report
  2. news:fetch по позициям
  3. Проверить активные рецепты
action:
  analyze_and_report: true
```

---

## 3. memory — Память пользователя

### Назначение
Хранение предпочтений, истории и контекста пользователя.

### Команды

```bash
# Получить/установить настройки
memory get profile.risk_tolerance
memory set profile.risk_tolerance moderate

memory get profile.sectors
memory set profile.sectors "нефтегаз,IT,финансы"

memory get profile.horizon
memory set profile.horizon long-term

# История сделок
memory add trade --ticker=SBER --action=buy --price=250 --date=2025-03-18
memory add trade --ticker=SBER --action=sell --price=280 --date=2025-04-15
memory get trades --ticker=SBER

# Заметки
memory add note --ticker=SBER "Хорошо показал себя в кризис 2022"
memory get notes --ticker=SBER

# Весь профиль
memory get profile
memory set profile --interactive  # Мастер настройки

# Экспорт/импорт
memory export > user_memory.json
memory import < user_memory.json
```

### Структура данных

```json
{
  "profile": {
    "experience": "intermediate",
    "risk_tolerance": "moderate",
    "horizon": "long-term",
    "strategy": ["dividend", "value"],
    "sectors": ["нефтегаз", "IT", "финансы"],
    "avoid": ["криптовалюты", "форекс"],
    "capital_base": 1000000,
    "max_position_pct": 10,
    "max_risk_per_trade_pct": 2
  },
  "trades": [
    {
      "ticker": "SBER",
      "action": "buy",
      "price": 250,
      "quantity": 100,
      "date": "2025-03-18",
      "recipe_id": "uuid"
    }
  ],
  "notes": [
    {
      "ticker": "SBER",
      "content": "Хорошо показал себя в кризис 2022",
      "date": "2025-03-18"
    }
  ],
  "preferences": {
    "notification_channels": ["telegram"],
    "report_frequency": "weekly",
    "report_day": "monday",
    "report_time": "09:00"
  }
}
```

---

## 4. calc — Калькуляторы и вычисления

### Назначение
Расчёт торговых метрик, математических выражений и выполнение Python кода.

### Аналоги Stonki AI:
- `evaluate_math_expression` → `calc eval`
- `code_execution` → `calc exec`

### Команды

#### Простые вычисления (evaluate_math_expression)
```bash
# Kelly Criterion
calc kelly --win-rate=0.55 --avg-win=0.15 --avg-loss=0.10

# Размер позиции
calc position-size \
  --capital=1000000 \
  --risk-pct=2 \
  --entry=260 \
  --stop=240

# Risk/Reward
calc rr --entry=260 --target=300 --stop=240

# Математическое выражение
calc eval --expression="(300-260)/(260-240)"
# Результат: 1.67 (R:R ratio)

# Процентное изменение
calc pct-change --from=240 --to=260

# Sharpe Ratio
calc sharpe --returns="0.05,-0.02,0.08,0.03,-0.01"

# Волатильность
calc volatility --ticker=SBER --period=30

# Дивидендная доходность
calc div-yield --ticker=SBER --price=260

# Справедливая цена (DCF упрощённый)
calc fair-value --ticker=SBER --growth=0.05 --discount=0.12
```

#### Python выполнение (code_execution)
```bash
# Выполнение файла
calc exec --file=black_scholes.py

# Выполнение кода inline
calc exec --code='
import numpy as np
from scipy.stats import norm

# Black-Scholes для опционов
S = 260      # цена акции
K = 250      # страйк
T = 90/365   # лет до экспирации
r = 0.12     # безрисковая ставка
sigma = 0.3  # волатильность

d1 = (np.log(S/K) + (r + 0.5*sigma**2)*T) / (sigma*np.sqrt(T))
d2 = d1 - sigma*np.sqrt(T)

call_price = S*norm.cdf(d1) - K*np.exp(-r*T)*norm.cdf(d2)
print(f"Call price: {call_price:.2f}")
'

# Опционные греки
calc greeks --spot=260 --strike=250 --days=90 --rate=0.12 --vol=0.3
# Результат: Delta, Gamma, Theta, Vega

# Статистика портфеля
calc exec --file=portfolio_stats.py --data=portfolio.json
```

**Доступные библиотеки:**
- numpy, scipy, pandas — математика и статистика
- matplotlib — визуализация (сохраняет в файл)
- scikit-learn — машинное обучение

### Примеры вывода

```
# calc position-size
Position Size Calculator
─────────────────────────────────────
Capital:          1,000,000 ₽
Risk per trade:   2% (20,000 ₽)
Entry:            260 ₽
Stop:             240 ₽
Risk per share:   20 ₽
─────────────────────────────────────
Max shares:       1,000
Position value:   260,000 ₽ (26% of capital)
─────────────────────────────────────
```

---

## 5. web — Получение веб-контента

### Назначение
Получение контента с веб-сайтов (аналог fetch_txt из Stonki).

### Команды

```bash
# Получить содержимое страницы
web fetch --url="https://sberbank.com/ru/investor_relations"

# С кастомными заголовками
web fetch --url="..." --header="User-Agent:Mozilla/5.0"

# Извлечь текст (без HTML)
web fetch --url="..." --text-only

# Извлечь таблицы
web fetch --url="..." --tables

# Поиск по странице
web fetch --url="..." --search="дивиденды"

# Кэширование (на 1 час)
web fetch --url="..." --cache=3600
```

### Примеры использования

```bash
# Отчётность компании
web fetch --url="https://www.sberbank.com/ru/investor_relations" \
  --search="отчётность" --text-only

# Дивидендная политика
web fetch --url="https://www.lukoil.ru/ru/Investors/dividendpolicy"

# Новости с сайта
web fetch --url="https://www.rbc.ru/economics/" --text-only

# Пресс-релиз
web fetch --url="https://www.gazprom.ru/press/news/" --tables
```

### Альтернатива через news skill

```bash
# Большинство контента можно получить через news
news news:fetch --search="Сбербанк отчётность"
news news:fetch --source=rbc --category=economics
```

**Приоритет:** LOW (использовать news skill вместо прямого fetch)

---

## 6. calendar — Календарь событий

### Назначение
Отслеживание отчётностей, дивидендов, собраний акционеров.

### Команды

```bash
# Отчётности
calendar earnings --period=week
calendar earnings --ticker=SBER
calendar earnings --sector=financial

# Дивиденды
calendar dividends --period=month
calendar dividends --ticker=SBER
calendar dividends --min-yield=10

# Собрания акционеров
calendar meetings --period=month

# Все события по портфелю
calendar portfolio --period=month

# Добавить событие
calendar add --ticker=SBER --type=earnings --date=2025-04-15
```

### Структура данных

```json
{
  "date": "2025-04-15",
  "ticker": "SBER",
  "type": "earnings",
  "description": "Отчётность за Q1 2025",
  "importance": "high",
  "source": "company"
}
```

---

## 6. social — Анализ настроений (российские соцсети)

### Назначение
Анализ настроений инвесторов в российских соцсетях и на форумах.

### Источники данных

| Источник | Приоритет | Тип | API/Метод |
|----------|-----------|-----|-----------|
| **Telegram** | HIGH | Каналы инвесторов | Telegram API |
| **Smart-Lab** | HIGH | Форум инвесторов | Парсинг |
| **Banki.ru** | MEDIUM | Отзывы, обсуждения | Парсинг |
| **VK** | LOW | Группы по инвестициям | VK API |
| **YouTube** | LOW | Комментарии на фин. каналах | YouTube API |
| **Dzen** | LOW | Статьи и комментарии | Парсинг |

### Популярные Telegram-каналы для анализа

```
financh, i_am_channel, russinvest, investbro, 
fingramotnost, rbk_invest, tinkoff_invest
```

### Команды

```bash
# Sentiment по тикеру
social sentiment --ticker=SBER
social sentiment --ticker=SBER --sources=telegram,smartlab

# По ключевым словам
social sentiment --keywords="Сбербанк,дивиденды"

# Топ обсуждаемых
social trending --period=24h
social trending --sector=financial

# История sentiment
social history --ticker=SBER --days=7

# Конкретный источник
social telegram --channel=financh --ticker=SBER
social smartlab --ticker=SBER --thread=latest
```

### Структура данных

```json
{
  "ticker": "SBER",
  "timestamp": "2025-03-18T12:00:00Z",
  "period_hours": 24,
  "overall_sentiment": 0.35,
  "mention_count": 245,
  "by_source": {
    "telegram": {
      "sentiment": 0.4,
      "mentions": 120,
      "top_channels": ["financh", "i_am_channel"],
      "keywords": ["дивиденды", "покупка", "рост"]
    },
    "smartlab": {
      "sentiment": 0.2,
      "mentions": 98,
      "threads": 12,
      "keywords": ["ставка ЦБ", "проценты"]
    },
    "banki_ru": {
      "sentiment": 0.5,
      "mentions": 27,
      "keywords": ["сервис", "надёжность"]
    }
  },
  "trending_topics": ["дивиденды", "ставка ЦБ", "отчётность"],
  "notable_posts": [
    {
      "source": "telegram",
      "channel": "financh",
      "text": "...",
      "sentiment": 0.8,
      "date": "2025-03-18T10:30:00Z"
    }
  ]
}
```

### Пример вывода

```
Social Sentiment: SBER (24h)
─────────────────────────────────────────────────
Overall Sentiment: +0.35 (Positive)
Total Mentions: 245

By Source:
┌─────────────┬───────────┬───────────┬─────────────────────┐
│ Источник    │ Sentiment │ Упоминаний│ Ключевые слова      │
├─────────────┼───────────┼───────────┼─────────────────────┤
│ Telegram    │ +0.40     │ 120       │ дивиденды, покупка  │
│ Smart-Lab   │ +0.20     │ 98        │ ставка ЦБ, проценты │
│ Banki.ru    │ +0.50     │ 27        │ сервис, надёжность  │
└─────────────┴───────────┴───────────┴─────────────────────┘

Trending Topics: дивиденды (45%), ставка ЦБ (30%), отчётность (25%)

Notable: @financh: "Сбер - лучшая покупка на просадках"
─────────────────────────────────────────────────
```

---

## Интеграция с существующими skills

### Workflow с новыми skills

```
┌─────────────────────────────────────────────────────────────────┐
│  Пользователь: "Хочу купить Сбер на 260"                        │
└─────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│  1. memory get profile → риск-профиль                           │
│  2. moex security:trade-data SBER → текущая цена                │
│  3. skill analyze:fundamental --ticker=SBER → метрики           │
│  4. news news:fetch --ticker SBER → новости                     │
└─────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│  5. calc position-size --entry=260 --stop=240 → размер позиции  │
│  6. calc rr --entry=260 --target=300 --stop=240 → R:R           │
└─────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│  7. recipe create --ticker=SBER ... → создание рецепта          │
│  8. monitor create price --ticker=SBER --level=260 → алерт      │
└─────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│  Ответ:                                                         │
│  "Создал рецепт на SBER. Текущая цена 255 ₽.                   │
│   R:R = 2:1. Рекомендуемый размер: 1000 акций (26% портфеля).  │
│   Алерт настроен на 260 ₽."                                    │
└─────────────────────────────────────────────────────────────────┘
```

---

## Приоритет реализации

| Skill | Приоритет | Сложность | Зависимости |
|-------|-----------|-----------|-------------|
| recipe | HIGH | Средняя | moex, skill |
| monitor | HIGH | Высокая | moex, recipe |
| memory | HIGH | Низкая | - |
| social | MEDIUM | Высокая | Telegram API, парсинг |
| calc | MEDIUM | Низкая | - |
| calendar | MEDIUM | Средняя | moex |
