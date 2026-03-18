# Multi-Agent Architecture

Архитектура субагентов для T-Invest Agent на основе подхода Stonki AI.

---

## Концепция

**Stonki AI:** Мультиагентная система с оркестратором (Claude), который делегирует задачи специализированным экспертам.

```
┌─────────────────────────────────────────────────────────────────┐
│                    ОРКЕСТРАТОР                                   │
│  (главный агент, общается с пользователем)                      │
│                                                                 │
│  • Анализ намерения запроса                                     │
│  • Выбор: ответить самому или делегировать                      │
│  • Координация нескольких агентов                               │
│  • Агрегация результатов                                        │
│  • Формирование финального ответа                               │
└─────────────────────────────────────────────────────────────────┘
                              │
         ┌────────────────────┼────────────────────┐
         │                    │                    │
         ▼                    ▼                    ▼
   ┌──────────┐        ┌──────────┐        ┌──────────┐
   │ TA Agent │        │News Agent│        │Memory    │
   │ (техника)│        │(новости) │        │Agent     │
   └──────────┘        └──────────┘        └──────────┘
         │                    │                    │
         └────────────────────┼────────────────────┘
                              ▼
                    ┌──────────────┐
                    │   TOOLS      │
                    │ (CLI skills) │
                    └──────────────┘
```

---

## Экспертные агенты

### 1. TA Agent (Технический анализ)

**Stonki:** `transfer_to_ta_agent(task_description)`

**Назначение:** Глубокий технический анализ — индикаторы, паттерны, уровни.

**Когда вызывать:**
- "Проанализируй техническую картину SBER"
- "Какой тренд у LKOH?"
- "Найди уровни поддержки/сопротивления"
- "Покажи сигналы RSI/MACD"

**task_description формат:**
```yaml
task: "Analyze technical setup for SBER"
context:
  ticker: "SBER"
  timeframe: "1d"
  current_price: 255
  include_pre_post: false  # вне торговых часов
required:
  - trend_direction
  - support_resistance_levels
  - rsi
  - macd
  - volume_analysis
  - chart_patterns
output_format: "structured_report"
```

**T-Invest Agent реализация:**
```bash
# Оркестратор вызывает:
skill analyze:technical --ticker=SBER --timeframe=1d

# Возвращает:
# | Метрика | Значение | Сигнал |
# |---------|----------|--------|
# | Trend   | ↑ Up     | Bullish|
# | RSI     | 45       | Neutral|
# | MACD    | Positive | Bullish|
# | Support | 240      | —      |
# | Resist  | 270      | —      |
```

---

### 2. News Agent (Анализ новостей)

**Stonki:** `transfer_to_news_agent(task_description)`

**Назначение:** Анализ новостей и их влияния на цену.

**Когда вызывать:**
- "Что нового по SBER?"
- "Как новости влияют на портфель?"
- "Были ли важные события по нефтегазу?"

**task_description формат:**
```yaml
task: "Analyze news impact for SBER"
context:
  ticker: "SBER"
  period_days: 7
  categories: ["corporate", "sector", "macro"]
required:
  - news_summary
  - sentiment: positive/negative/neutral
  - price_impact_assessment
  - key_events
output_format: "structured_report"
```

**T-Invest Agent реализация:**
```bash
# Оркестратор вызывает:
news news:fetch --ticker SBER --days=7

# + анализ sentiment оркестратором
# Возвращает:
# | Дата | Заголовок | Sentiment | Влияние |
# |------|-----------|-----------|---------|
# | 18.03| Сбербанк...| Positive  | Medium  |
```

---

### 3. Scanner Agent (Сканирование рынка)

**Stonki:** `transfer_to_scanner_agent(task_description)`

**Назначение:** Сканирование рынка по техническим/фундаментальным критериям.

**Когда вызывать:**
- "Найди недооценённые акции"
- "Покажи с RSI < 30"
- "Какие акции растут 3 дня подряд?"

**task_description формат:**
```yaml
task: "Scan market for oversold stocks"
criteria:
  type: "technical" | "fundamental" | "mixed"
  filters:
    - rsi: "< 30"
    - volume: "> 2x average"
    - sector: "all" | "financial"
    - market_cap: "> 10B"
  sort_by: "rsi" | "volume" | "change_pct"
  limit: 20
output_format: "ranked_list"
```

**T-Invest Agent реализация:**
```bash
# Оркестратор вызывает:
skill screen:stocks --rsi-max=30 --volume-mult=2 --limit=20

# Возвращает:
# | Ticker | RSI | Volume | Change | Score |
# |--------|-----|--------|--------|-------|
# | GAZP   | 25  | 3.2x   | -5%    | 9/10  |
```

---

### 4. Social Media Agent (Российские соцсети) — MEDIUM PRIORITY

**Stonki:** `transfer_to_social_media_agent(task_description)`

**Назначение:** Анализ настроений в российских соцсетях.

**Российские источники:**
- **Telegram** — инвестиционные каналы (financh, i\_am\_channel, и др.)
- **VK** — группы по инвестициям
- **YouTube** — комментарии на финансовых каналах
- **Banki.ru** — отзывы и обсуждения
- **Smart-Lab** — форум инвесторов
- **Dzen** — статьи и комментарии

**task_description формат:**
```yaml
task: "Analyze social sentiment for SBER"
sources:
  - telegram_channels: ["financh", "i_am_channel", "russinvest"]
  - smartlab_forum: true
  - banki_ru: true
keywords: ["SBER", "Сбербанк", "Sber", "сбер"]
period_hours: 24
required:
  - sentiment_score: -1 to +1
  - mention_count
  - trending_topics
  - notable_posts
  - expert_opinions
```

**T-Invest Agent реализация:**
```bash
# Новый skill для соцсетей
social sentiment --ticker=SBER --sources=telegram,smartlab

# Возвращает:
# | Источник   | Sentiment | Упоминаний | Ключевые темы |
# |------------|-----------|------------|---------------|
# | Telegram   | +0.3      | 45         | дивиденды     |
# | SmartLab   | -0.1      | 128        | ставка ЦБ     |
# | Banki.ru   | +0.2      | 23         | сервис        |
# | ИТОГО      | +0.15     | 196        | —             |
```

**Приоритетные источники для MOEX:**

| Источник | Приоритет | API/Парсинг |
|----------|-----------|-------------|
| Telegram каналы | HIGH | Telegram API |
| Smart-Lab форум | HIGH | Парсинг |
| Banki.ru | MEDIUM | Парсинг |
| VK группы | LOW | VK API |
| YouTube комменты | LOW | YouTube API |

---

### 5. Web Search Agent (Веб-поиск) — LOW PRIORITY

**Stonki:** `transfer_to_websearch_agent(task_description)`

**Назначение:** Поиск актуальной информации в интернете.

**task_description формат:**
```yaml
task: "Search for SBER recent news"
query: "Сбербанк дивиденды 2025"
max_results: 5
```

**T-Invest Agent:** Может использовать news skill с `--search`.

---

### 6. User Memory Agent (Память пользователя) — MEDIUM PRIORITY

**Stonki:** `transfer_to_user_memory_agent(task_description)`

**Назначение:** Доступ к истории предпочтений и прошлых сделок.

**task_description формат:**
```yaml
task: "Retrieve user context for SBER analysis"
query:
  - risk_tolerance
  - past_trades: ["SBER"]
  - preferences: ["sectors", "strategy"]
  - active_recipes: true
```

**T-Invest Agent реализация:**
```bash
# Оркестратор вызывает:
memory get profile
memory get trades --ticker=SBER

# Возвращает:
# | Параметр | Значение |
# |----------|----------|
# | Risk     | Moderate |
# | Sectors  | Нефтегаз |
# | SBER trades | 3 прошлых |
```

---

## Workflow делегирования

### Пример: "Проанализируй SBER, думаю купить"

```
[Пользователь] "Проанализируй SBER, думаю купить"

[Оркестратор - анализ намерения]
→ Это запрос на анализ + потенциальная торговая идея
→ Нужны: цена, техника, фундаментал, новости, память пользователя

[Оркестратор - вызов агентов]

1. memory get profile → риск-профиль пользователя
   └─→ { risk: moderate, sectors: [нефтегаз], max_position: 10% }

2. moex security:trade-data SBER → текущая цена
   └─→ { price: 255, volume: 2.5B }

3. transfer_to_ta_agent("Analyze SBER technical")
   └─→ { trend: sideways, RSI: 45, support: 240, resist: 270 }

4. transfer_to_news_agent("Analyze SBER news 7 days")
   └─→ { sentiment: neutral, events: 2 minor }

5. skill analyze:fundamental --ticker=SBER
   └─→ { P/E: 5.2, Div: 12%, ROE: 22% }

[Оркестратор - агрегация]
→ Техника: нейтрально (боковик)
→ Фундаментал: позитивно (низкий P/E, высокие дивиденды)
→ Новости: нейтрально
→ Риск-профиль: подходит под moderate

[Оркестратор - решение]
→ Торговая идея есть? Да
→ Создать рецепт? Да
→ Создать мониторинг? Да

[Оркестратор - действия]
recipe create --ticker=SBER ...
monitor create price --ticker=SBER --level=255

[Оркестратор - ответ]
"SBER: HOLD → BUY на просадках
 Цена: 255 ₽ | P/E: 5.2 | Дивиденды: 12%
 Техника: боковик 240-270
 Рекомендация: рассмотреть покупку на 240-245
 
 Рецепт создан: long-sber-dividends
 Алерт настроен: 240 ₽
 
 Не является инвестиционной рекомендацией.
 SBER"
```

---

## Правила делегирования

### Когда делегировать

| Запрос содержит | Делегировать |
|-----------------|--------------|
| "технический", "индикаторы", "тренд" | TA Agent |
| "новости", "события", "что случилось" | News Agent |
| "найди", "скрининг", "фильтр" | Scanner Agent |
| "мои настройки", "история", "профиль" | Memory Agent |
| Комплексный анализ | Несколько агентов |

### Когда НЕ делегировать

- Простой запрос цены → `moex security:trade-data`
- Простой запрос метрик → `skill analyze:quick`
- Управление рецептами → `recipe create/list/update`
- Управление мониторингом → `monitor create/list`

---

## Реализация в T-Invest Agent

### Текущее состояние

| Stonki Agent | T-Invest Agent | Реализовано |
|--------------|----------------|-------------|
| `transfer_to_ta_agent` | `skill analyze:technical` | ✅ |
| `transfer_to_news_agent` | `news news:fetch` | ✅ |
| `transfer_to_scanner_agent` | `skill screen:stocks` | ✅ |
| `transfer_to_user_memory_agent` | `memory` (новый) | ❌ TODO |
| `transfer_to_social_media_agent` | `social sentiment` (новый) | ❌ TODO — российские соцсети |
| `transfer_to_websearch_agent` | — | ❌ LOW |

### Приоритеты для российского рынка

| Агент | Приоритет | Источники |
|-------|-----------|-----------|
| TA Agent | ✅ Готов | MOEX данные |
| News Agent | ✅ Готов | Interfax, TASS, RBC |
| Scanner Agent | ✅ Готов | MOEX + T-Invest |
| Memory Agent | HIGH | Локальное хранение |
| Social Agent | MEDIUM | Telegram, Smart-Lab, Banki.ru |
| Web Search | LOW | Яндекс, Google |

### Что нужно добавить

1. **Явная координация в AGENTS.md** — правила когда вызывать какие skills
2. **Memory skill** — для transfer_to_user_memory_agent
3. **Task description формат** — стандартизировать параметры вызова

---

## Интеграция с AGENTS.md

```markdown
## Экспертные агенты

Оркестратор делегирует задачи специализированным skills:

### TA Agent → skill analyze:technical
Вызывать когда:
- Пользователь спрашивает про техническую картину
- Нужен анализ тренда, уровней, индикаторов
- Проверка сигналов перед сделкой

### News Agent → news news:fetch
Вызывать когда:
- Пользователь спрашивает про новости
- Нужен sentiment анализ
- Проверка событий перед сделкой

### Scanner Agent → skill screen:stocks
Вызывать когда:
- Пользователь просит найти акции по критериям
- Нужен скрининг недооценённых/перекупленных

### Memory Agent → memory (TODO)
Вызывать когда:
- Нужен профиль пользователя
- Нужна история сделок по тикеру
- Нужно учесть предпочтения в рекомендации
```
