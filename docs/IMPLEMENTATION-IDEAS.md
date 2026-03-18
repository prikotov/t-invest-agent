# Implementation Ideas

Полный список идей для реализации в T-Invest Agent на основе анализа Stonki AI.

---

## 1. Recipes (Рецепты) — HIGH PRIORITY

### Концепция
Живые торговые журналы для отслеживания идей от тезиса до результата.

### Что нужно реализовать

#### 1.1 CLI команды
```bash
recipe create --ticker=SBER --thesis="..." --entry=260 --target=300 --stop=240
recipe list [--status=active|closed] [--ticker=SBER]
recipe update --id=UUID --note="Цена достигла уровня"
recipe close --id=UUID --result=+15%
recipe history --ticker=SBER
recipe stats --period=month
```

#### 1.2 Структура данных
```json
{
  "id": "uuid",
  "ticker": "SBER",
  "status": "active",
  "thesis": { "summary": "...", "rationale": "..." },
  "entry": { "zone": [250, 260], "filled": false },
  "target": { "price": 300, "rationale": "..." },
  "stop": { "price": 240, "type": "hard" },
  "risk_reward": 2.5,
  "meta_data": {
    "tickers": ["SBER"],
    "timeframes": ["1d"],
    "tags": ["dividend", "value"]
  },
  "updates": [],
  "result": null
}
```

#### 1.3 Автоматические действия
- Создавать рецепт при торговой идее пользователя
- Обновлять при срабатывании мониторинга
- Рассчитывать R:R автоматически

#### 1.4 Интеграция с AGENTS.md
```markdown
## Когда создавать рецепт

АВТОМАТИЧЕСКИ когда пользователь:
- Описывает торговую идею с уровнями
- Спрашивает про тикер с намерением торговли
- Просит оценить точку входа
```

---

## 2. Monitoring (Мониторинг) — HIGH PRIORITY

### Концепция
Система отслеживания 24/7: ценовые алерты, тех. сигналы, периодические проверки.

### Что нужно реализовать

#### 2.1 CLI команды
```bash
# Ценовой алерт
monitor create price --ticker=SBER --level=260 --direction=above

# Технический сигнал
monitor create signal --ticker=SBER --indicator=RSI --condition="<30"

# Периодическая проверка
monitor create schedule --cron="0 9 * * 1-5" --prompt="Проверить портфель"

# Управление
monitor list
monitor pause --id=UUID
monitor delete --id=UUID

# Демон
monitor daemon start
monitor daemon stop
```

#### 2.2 Типы мониторинга

**price_cross:**
```yaml
ticker: SBER
condition: price >= 260
action:
  notify: true
  update_recipe: true
```

**technical_signal:**
```yaml
ticker: SBER
condition:
  indicator: RSI
  operator: <
  value: 30
action:
  notify: true
```

**recurring_prompt:**
```yaml
schedule: "0 9 * * 1-5"
prompt: |
  1. portfolio:report
  2. news:fetch по позициям
  3. Проверить активные рецепты
action:
  analyze_and_report: true
```

#### 2.3 Демон мониторинга
- Запуск как systemd service или cron
- Проверка условий каждую минуту
- Отправка уведомлений (Telegram, Email)
- Обновление рецептов при срабатывании

---

## 3. User Memory (Память) — MEDIUM PRIORITY

### Концепция
Хранение предпочтений, истории и контекста пользователя.

### Что нужно реализовать

#### 3.1 CLI команды
```bash
# Профиль
memory get profile
memory set profile.risk_tolerance moderate
memory set profile.sectors "нефтегаз,IT"

# История сделок
memory add trade --ticker=SBER --action=buy --price=250
memory get trades --ticker=SBER

# Заметки
memory add note --ticker=SBER "Хорош в кризис"
memory get notes --ticker=SBER

# Экспорт/импорт
memory export > user_memory.json
memory import < user_memory.json
```

#### 3.2 Структура данных
```json
{
  "profile": {
    "experience": "intermediate",
    "risk_tolerance": "moderate",
    "horizon": "long-term",
    "strategy": ["dividend", "value"],
    "sectors": ["нефтегаз", "IT"],
    "max_position_pct": 10,
    "max_risk_per_trade_pct": 2
  },
  "trades": [],
  "notes": [],
  "preferences": {
    "notification_channels": ["telegram"],
    "report_frequency": "weekly"
  }
}
```

#### 3.3 Иерархия памяти
```
1. User Memory (персональное) — высший приоритет
2. Global Memory (общее)
3. General Guidelines (базовое)
```

---

## 4. Calculators (Калькуляторы) — MEDIUM PRIORITY

### Концепция
Расчёт торговых метрик и управление риском.

### Что нужно реализовать

#### 4.1 CLI команды
```bash
# Размер позиции
calc position-size --capital=1000000 --risk-pct=2 --entry=260 --stop=240

# Risk/Reward
calc rr --entry=260 --target=300 --stop=240

# Kelly Criterion
calc kelly --win-rate=0.55 --avg-win=0.15 --avg-loss=0.10

# Sharpe Ratio
calc sharpe --returns="0.05,-0.02,0.08"

# Дивидендная доходность
calc div-yield --ticker=SBER --price=260
```

#### 4.2 Пример вывода
```
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

## 5. Onboarding (Онбординг) — LOW PRIORITY

### Концепция
Интерактивное заполнение инвестпрофиля при первом запуске.

### Что нужно реализовать

#### 5.1 Интерактивный мастер
```bash
./bin/agent onboard

? Ваш опыт в инвестициях? (beginner/intermediate/advanced)
? Ваша толерантность к риску? (low/moderate/high)
? Ваш инвестиционный горизонт? (day/swing/long-term)
? Какие сектора вам интересны? (выбор из списка)
? Какой макс. % портфеля на одну позицию?
? Какой макс. % риска на сделку?
```

#### 5.2 Сохранение в memory
```bash
memory set profile --from-onboard
```

---

## 6. Ticker Tracking — LOW PRIORITY

### Концепция
Автоматическое добавление тикеров в конец каждого ответа.

### Что нужно реализовать

#### 6.1 Правила
- Извлекать тикеры из контекста
- Добавлять в конец ответа
- Использовать теги `<TICKERS>` для внутренней обработки

#### 6.2 Пример
```
SBER торгуется на 255 ₽. Техническая картина...
[анализ]
Не является инвестиционной рекомендацией.
SBER
```

---

## 7. Calendar — LOW PRIORITY

### Концепция
Календарь событий: отчётности, дивиденды, собрания.

### Что нужно реализовать

#### 7.1 CLI команды
```bash
calendar earnings --period=week
calendar dividends --period=month --min-yield=10
calendar meetings --period=month
calendar portfolio --period=month
```

#### 7.2 Источники данных
- MOEX ISS API (дивиденды, собрания)
- Компания (отчётности)
- Ручное добавление

---

## 8. System Prompts — HIGH PRIORITY

### Концепция
5-уровневая иерархия системных инструкций.

### Что нужно реализовать

#### 8.1 Обновить AGENTS.md

```markdown
# УРОВЕНЬ 1: META-ПРАВИЛА

## professional_communication
- Тон: прямой, основанный на данных
- Без эмодзи в анализе

## data_freshness
- КРИТИЧНО: всегда использовать инструменты
- Никогда не предполагать цены

## mandatory_disclaimer
- КАЖДЫЙ ответ: "Не является инвестиционной рекомендацией."

# УРОВЕНЬ 2: DOMAIN LOGIC

## recipe_philosophy
- Рецепты = живые документы
- Эволюция через обновления

## monitoring_philosophy
- 3 типа: price_cross, signal, schedule
- Когда использовать какой

# УРОВЕНЬ 3: SAFETY

## limitations
- НЕ МОГУ: давать советы, гарантировать прибыль
- МОГУ: анализировать, рекомендовать, отслеживать

# УРОВЕНЬ 4: OUTPUT

## response_format
- Структурированные ответы
- Таблицы для метрик
- Дисклеймер + тикеры

# УРОВЕНЬ 5: CONTEXT

## user_preferences
- Профиль из memory
- Активные рецепты
- Активный мониторинг
```

---

## 9. Cognitive Architecture — MEDIUM PRIORITY

### Концепция
Архитектура мышления агента с защитными механизмами.

### Что нужно реализовать

#### 9.1 Метакогнитивные проверки
```markdown
При каждом ответе агент проверяет:

1. Уверен ли я? → Если нет, запросить данные
2. Безопасно ли? → Добавить disclaimer
3. Не предполагаю ли? → Использовать инструменты
4. Помогает ли пользователю? → Фокус на actionable
5. Прозрачен ли? → Честность об ограничениях
```

#### 9.2 Защитные механизмы
- Hallucination Prevention
- Compliance Enforcement
- Quality Gates

---

## 10. Social Media Agent (Российские соцсети) — MEDIUM PRIORITY

### Концепция
Анализ настроений инвесторов в российских соцсетях для принятия решений.

### Что нужно реализовать

#### 10.1 Источники данных

| Источник | Приоритет | Метод |
|----------|-----------|-------|
| Telegram (фин. каналы) | HIGH | Telegram API |
| Smart-Lab (форум) | HIGH | Парсинг |
| Banki.ru | MEDIUM | Парсинг |
| VK (группы) | LOW | VK API |

#### 10.2 CLI команды
```bash
# Sentiment по тикеру
social sentiment --ticker=SBER

# По ключевым словам
social sentiment --keywords="нефть,ОПЕК+"

# Топ обсуждаемых
social trending --period=24h

# Конкретный канал
social telegram --channel=financh --ticker=SBER
```

#### 10.3 Примеры Telegram-каналов
```
financh, i_am_channel, russinvest, investbro, 
fingramotnost, rbk_invest, tinkoff_invest
```

#### 10.4 Структура ответа
```
Social Sentiment: SBER (24h)
─────────────────────────────────────────────────
Overall: +0.35 (Positive) | Mentions: 245

Telegram:  +0.40 (120 mentions) | дивиденды, покупка
Smart-Lab: +0.20 (98 mentions)  | ставка ЦБ
Banki.ru:  +0.50 (27 mentions)  | сервис

Trending: дивиденды (45%), ставка ЦБ (30%)
─────────────────────────────────────────────────
```

---

## 11. Notification Channels — MEDIUM PRIORITY

### Концепция
Отправка уведомлений при срабатывании мониторинга.

### Что нужно реализовать

#### 10.1 Каналы
- Telegram Bot
- Email
- Push (опционально)

#### 10.2 Формат уведомления
```
🔔 Алерт: SBER

Цена достигла 260 ₽

Рецепт: long-sber-dividends
Рекомендация: Рассмотреть вход

Не является инвестиционной рекомендацией.
```

---

## Приоритеты реализации

### Phase 1: Foundation (2-3 недели)
- [ ] Recipe skill
- [ ] Monitor skill (демон)
- [ ] Memory skill
- [ ] Обновить AGENTS.md

### Phase 2: Enhancement (1-2 недели)
- [ ] Calc skill
- [ ] Calendar skill (отчётности)
- [ ] Social skill (Telegram, Smart-Lab)
- [ ] Интеграция в workflow

### Phase 3: Polish (1 неделя)
- [ ] Onboarding
- [ ] Ticker tracking
- [ ] Telegram Bot (уведомления)

---

## Метрики успеха

| Метрика | Цель |
|---------|------|
| Время ответа на анализ | < 30 сек |
| Точность данных | 100% (из API) |
| Дисклеймер в ответах | 100% |
| Uptime мониторинга | 99.9% |
| Время создания рецепта | < 5 сек |
