# Stonki AI Analysis

Полный анализ возможностей Stonki AI для адаптации в T-Invest Agent.

## Ключевые концепции

### 1. Recipes (Рецепты)

**Определение:** Живые торговые журналы, которые эволюционируют вместе с рынком.

**API из Stonki:**

| Tool | Назначение | Параметры |
|------|------------|-----------|
| `create_recipe` | Создание | title, content, expected_position_schemas, meta_data |
| `update_recipe` | Обновление | recipe_id, title, content, schemas, meta_data |
| `edit_recipe_content` | Точечное редактирование | recipe_id, old_text, new_text |

#### create_recipe
```yaml
title: "long-sber-dividends"        # ОБЯЗАТЕЛЬНО, kebab-case
content: |                          # ОБЯЗАТЕЛЬНО, полный анализ
  ## Thesis
  Дивидендный доход 12%+, стабильные выплаты
  
  ## Entry
  Зона: 250-260 ₽
  
  ## Target
  300 ₽ (+17%)
  
  ## Stop
  240 ₽ (-6%)
  
  ## Risk/Reward
  2.7:1
  
  ## Position Size
  5% портфеля
expected_position_schemas:          # ОПЦИОНАЛЬНО, JSON Schema для P&L
  type: object
  properties:
    entry_price: { type: number }
    quantity: { type: integer }
    current_pnl: { type: number }
meta_data:                          # ОПЦИОНАЛЬНО
  tickers: ["SBER"]
  timeframes: ["1d"]
  tags: ["dividend", "value", "swing-trade"]
  created_at: "2025-03-18"
  status: "active"
```

#### update_recipe
```yaml
recipe_id: "550e8400-e29b-41d4-a716-446655440000"  # ОБЯЗАТЕЛЬНО
title: "long-sber-dividends-updated"  # ОПЦИОНАЛЬНО
content: |                              # ОПЦИОНАЛЬНО, добавлять, не перезаписывать!
  ## Update 2025-03-20
  Цена достигла 255 ₽, частичный вход 50% позиции
  Техническая картина без изменений
expected_position_schemas: {...}        # ОПЦИОНАЛЬНО
meta_data: {...}                        # ОПЦИОНАЛЬНО
```

**Важно:** content добавляется к существующему, не перезаписывает!

#### edit_recipe_content
```yaml
recipe_id: "550e8400-e29b-41d4-a716-446655440000"  # ОБЯЗАТЕЛЬНО
old_text: "Зона: 250-260 ₽"            # ОБЯЗАТЕЛЬНО, точный текст для замены
new_text: "Зона: 248-258 ₽"            # ОБЯЗАТЕЛЬНО, новый текст
```

**Использование:** Для точечных правок без перезаписи всего контента.

**Структура рецепта:**
```
title: "long-aapl-earnings-beat"  # kebab-case
content: полный анализ (тезис, уровни, риск, цели)
expected_position_schemas: JSON Schema для отслеживания P&L
meta_data: {
  tickers: ['AAPL'],
  timeframes: ['5m'],
  tags: ['swing-trade', 'earnings']
}
```

**Когда создавать автоматически:**
- Пользователь описывает торговую идею с уровнями
- Пользователь спрашивает про тикер с намерением торговли
- Пользователь просит оценить точку входа

**Когда обновлять:**
- При срабатывании алертов мониторинга
- При запланированных проверках (scheduled prompts)
- При рыночных событиях
- При обновлениях позиций пользователя

---

### 2. Monitoring Tasks (Мониторинг)

**Три типа мониторинга:**

#### 2.1 price_cross (ценовой алерт)
```yaml
template_id: "7bf22f73-c9f4-45f3-9d58-38289b730ea1"
params:
  stock_symbol: "SBER"               # 1-5 букв
  bar_size: "1d"                     # 1m, 5m, 15m, 30m, 1h, 4h, 1d
  price_threshold: 260               # уровень цены
```

#### 2.2 iv_cross (волатильность)
```yaml
template_id: "b81f57c9-a86b-40c4-95ea-5e276b32012b"
params:
  underlying_symbol: "AAPL"          # базовый актив
  iv_threshold: 0.35                 # порог implied volatility
```

#### 2.3 recurring_prompt (запланированные проверки)
```yaml
template_id: "a4b43edb-2758-4f4c-9969-874c397423fd"
params:
  rrule: "FREQ=DAILY;BYHOUR=9;BYMINUTE=30"  # RFC 5545
  prompt: "Проверить портфель и новости по позициям"
```

**API мониторинга:**

| Tool | Назначение |
|------|------------|
| `create_monitoring_task` | Создание (с template или custom) |
| `update_monitoring_task` | Изменение параметров |
| `pause_monitoring_task` | Пауза |

#### create_monitoring_task
```yaml
name: "SBER price alert 260"         # ОБЯЗАТЕЛЬНО, буквы/цифры/пробелы/дефисы
definition: "условия для отслеживания" # ОБЯЗАТЕЛЬНО если без template
template_id: "7bf22f73-..."          # ОПЦИОНАЛЬНО, ID шаблона
template_params:                     # ОПЦИОНАЛЬНО, параметры для шаблона
  stock_symbol: "SBER"
  bar_size: "1d"
  price_threshold: 260
expires_at: "2025-04-18T00:00:00Z"   # ОПЦИОНАЛЬНО, когда истекает
```

**Примеры использования:**

```yaml
# Через шаблон (рекомендуется)
create_monitoring_task:
  name: "SBER alert 260"
  template_id: "7bf22f73-..."  # price_cross
  template_params:
    stock_symbol: "SBER"
    bar_size: "1d"
    price_threshold: 260

# Custom определение
create_monitoring_task:
  name: "SBER RSI oversold"
  definition: |
    Monitor SBER RSI(14) on daily timeframe.
    Alert when RSI < 30.
    Include current price and volume in alert.
  expires_at: "2025-04-18"
```

#### update_monitoring_task
```yaml
monitoring_task_id: "550e8400-..."   # ОБЯЗАТЕЛЬНО
definition: "новые условия"           # ОПЦИОНАЛЬНО, только для статуса NEW
parameters: {...}                     # ОПЦИОНАЛЬНО, если есть params_schema
```

**Важно:** definition можно менять только если задача в статусе NEW!

#### pause_monitoring_task
```yaml
monitoring_task_id: "550e8400-..."   # ОБЯЗАТЕЛЬНО
```

**Использование:**
- Временно отключить алерт без удаления
- После паузы можно возобновить (resume)

---

### 2.1 Доступные шаблоны мониторинга

| Шаблон | ID | Параметры |
|--------|-----|-----------|
| **price_cross** | `7bf22f73-c9f4-45f3-9d58-38289b730ea1` | stock_symbol, bar_size, price_threshold |
| **iv_cross** | `b81f57c9-a86b-40c4-95ea-5e276b32012b` | underlying_symbol, iv_threshold |
| **recurring_prompt** | `a4b43edb-2758-4f4c-9969-874c397423fd` | rrule (RFC 5545), prompt |

---

### 2.2 Когда использовать какой шаблон

| Запрос пользователя | Шаблон | Пример |
|---------------------|--------|--------|
| "Сообщи когда SBER будет 260" | `price_cross` | price_threshold: 260 |
| "Следи когда SBER упадёт до 240" | `price_cross` | price_threshold: 240, direction: below |
| "Алерт если волатильность > 40%" | `iv_cross` | iv_threshold: 0.40 |
| "Каждое утро проверяй портфель" | `recurring_prompt` | FREQ=DAILY;BYHOUR=9 |
| "Каждую пятницу отчёт" | `recurring_prompt` | FREQ=WEEKLY;BYDAY=FR |
| "Каждый час проверяй цену" | `recurring_prompt` | FREQ=HOURLY |

**Для T-Invest Agent (MOEX):**

| Шаблон | Приоритет | Примечание |
|--------|-----------|------------|
| `price_cross` | HIGH | Основной тип алертов |
| `recurring_prompt` | HIGH | Ежеутренние/еженедельные проверки |
| `iv_cross` | LOW | Опционы на MOEX менее ликвидны |

---

### 2.3 RFC 5545 примеры (rrule)

```yaml
# Каждый день в 09:30
"FREQ=DAILY;BYHOUR=9;BYMINUTE=30"

# Каждый будний день в 18:00
"FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR;BYHOUR=18"

# Каждую пятницу в 09:00
"FREQ=WEEKLY;BYDAY=FR;BYHOUR=9"

# Каждый час
"FREQ=HOURLY"

# Каждые 4 часа
"FREQ=HOURLY;INTERVAL=4"

# Раз в месяц, 1-е число, 10:00
"FREQ=MONTHLY;BYMONTHDAY=1;BYHOUR=10"
```

---

### 3. Expert Agents (Экспертные агенты)

**Мультиагентная архитектура:**

| Agent | Назначение | Параметр |
|-------|------------|----------|
| `transfer_to_ta_agent` | Технический анализ | task_description |
| `transfer_to_news_agent` | Анализ новостей | task_description |
| `transfer_to_scanner_agent` | Сканирование рынка | task_description |
| `transfer_to_social_media_agent` | Соцсети (Twitter, Reddit) | task_description |
| `transfer_to_websearch_agent` | Веб-поиск | task_description |
| `transfer_to_user_memory_agent` | Память пользователя | task_description |

**Важно:** Каждый агент может использовать свою модель, оптимизированную под задачу.

---

### 4. Market Data Tools

| Tool | Назначение | Возвращает |
|------|------------|------------|
| `get_most_recent_bar` | OHLCV данные | open, high, low, close, volume, vwap |
| `get_option_chain` | Опционные цепочки | цены, греки, IV, объём |
| `get_ticker_details` | Информация о компании | детальные данные |
| `get_symbol_summaries` | Краткие метрики | performance, fundamentals, earnings |
| `get_week_earnings_events` | Календарь отчётностей | события недели |

#### get_most_recent_bar (OHLCV)
```yaml
symbol_list: ["AAPL", "MSFT"]  # или фьючерсы ["FUTURES:CME_MINI:ES1!"]
bar_size: 1, 5, 15  # число
bar_unit: "Second" | "Minute" | "Hour" | "Daily" | "Weekly" | "Monthly"
```
**Возвращает:** OHLCV + VWAP для акций

**Аналог в T-Invest Agent:**
```bash
moex security:trade-data SBER
# TODO: добавить параметры --bar-size и --bar-unit для истории
```

#### get_option_chain (Опционы)
```yaml
symbol: "AAPL"                    # базовый актив
strike_price_gte: 150             # мин. страйк
strike_price_lte: 200             # макс. страйк
expiration_date: "2025-04-15"     # точная дата (опционально)
expiration_date_gte: "2025-03-01" # от даты (опционально)
expiration_date_lte: "2025-06-01" # до даты (опционально)
option_type: "call" | "put"       # тип (опционально)
limit: 100                        # макс. контрактов
```
**Возвращает:** TSV с ценами, греками (Delta, Gamma, Theta, Vega), IV, объёмом

**Аналог в T-Invest Agent:**
```bash
# TODO: опционы на MOEX требуют отдельной интеграции
# moex options:chain SBER --expiration=2025-06
```

#### get_ticker_details (Компания)
```yaml
symbol: "AAPL"  # тикер
```
**Возвращает:** Детальная информация о компании и тикере

**Аналог в T-Invest Agent:**
```bash
moex security:specification SBER
# ISIN, List Level, Type, Issue Size, и т.д.
```

#### get_symbol_summaries (Метрики)
```yaml
symbols: "AAPL,MSFT,TSLA"  # список через запятую
```
**Возвращает:** CSV с краткими метриками:
- Performance (доходность за периоды)
- Fundamentals (P/E, P/B, ROE, EPS)
- Earnings (дата отчётности, прогноз)
- Technical indicators (RSI, MA)

**Аналог в T-Invest Agent:**
```bash
skill analyze:quick --ticker=SBER
# TODO: добавить пакетный режим для нескольких тикеров
```

#### get_week_earnings_events (Календарь)
```yaml
country: "US"              # код страны (по умолчанию)
limit: 10                  # кол-во событий (макс 30)
offset: 0                  # смещение для пагинации
min_market_cap: 1000000000 # мин. капитализация в USD
tickers: ["AAPL", "MSFT"]  # фильтр по тикерам (опционально)
```
**Возвращает:** Календарь отчётностей на неделю

**Аналог в T-Invest Agent:**
```bash
calendar earnings --period=week --min-market-cap=1000000000
calendar earnings --ticker=SBER
# TODO: интеграция с источниками данных MOEX
```

---

### 5. Additional Tools (Дополнительные возможности)

| Tool | Назначение | Параметры |
|------|------------|-----------|
| `fetch_txt` | Получение контента с веб-сайтов | url, headers (опционально) |
| `evaluate_math_expression` | Математические расчёты | expression |
| `code_execution` | Выполнение Python кода | code |

#### fetch_txt (Веб-контент)
```yaml
url: "https://example.com/report"    # ОБЯЗАТЕЛЬНО
headers:                              # ОПЦИОНАЛЬНО
  User-Agent: "Mozilla/5.0"
  Accept: "text/html"
```

**Возвращает:** Текстовое содержимое страницы

**Использование:**
- Получение отчётностей компаний с сайтов
- Чтение аналитических статей
- Извлечение данных из пресс-релизов

**Аналог в T-Invest Agent:**
```bash
# TODO: добавить инструмент для получения веб-контента
web fetch --url="https://sberbank.com/ru/investor_relations"
# или использовать через news skill
```

#### evaluate_math_expression (Математика)
```yaml
expression: "(300-260)/(260-240)"    # ОБЯЗАТЕЛЬНО, математическое выражение
```

**Возвращает:** Результат вычисления

**Использование:**
- Быстрые расчёты R:R
- Процентные изменения
- Формулы риск-менеджмента

**Аналог в T-Invest Agent:**
```bash
calc eval --expression="(300-260)/(260-240)"
calc rr --entry=260 --target=300 --stop=240
```

#### code_execution (Python)
```yaml
code: |                              # ОБЯЗАТЕЛЬНО, Python 3 код
  import numpy as np
  from scipy.stats import norm
  
  # Black-Scholes для опционов
  def black_scholes(S, K, T, r, sigma, option_type='call'):
      d1 = (np.log(S/K) + (r + 0.5*sigma**2)*T) / (sigma*np.sqrt(T))
      d2 = d1 - sigma*np.sqrt(T)
      
      if option_type == 'call':
          return S*norm.cdf(d1) - K*np.exp(-r*T)*norm.cdf(d2)
      else:
          return K*np.exp(-r*T)*norm.cdf(-d2) - S*norm.cdf(-d1)
  
  # Расчёт
  price = black_scholes(260, 250, 0.25, 0.12, 0.3, 'call')
  print(f"Call price: {price:.2f}")
```

**Доступные библиотеки:** pandas, numpy, matplotlib, scikit-learn, scipy

**Возвращает:** stdout выполнения кода

**Использование:**
- Расчёты опционов (Black-Scholes, греки)
- Статистический анализ
- Визуализация данных
- Сложные финансовые модели

**Аналог в T-Invest Agent:**
```bash
# TODO: добавить Python execution
calc exec --file=black_scholes.py
calc exec --code="import numpy as np; print(np.mean([1,2,3]))"

# Или предустановленные калькуляторы
calc option-price --spot=260 --strike=250 --days=90 --rate=0.12 --vol=0.3
calc greeks --spot=260 --strike=250 --days=90 --rate=0.12 --vol=0.3
```

**Приоритет реализации:**

| Tool | Приоритет | Примечание |
|------|-----------|------------|
| `evaluate_math_expression` | HIGH | Простые расчёты R:R, % |
| `code_execution` | MEDIUM | Опционы, статистика |
| `fetch_txt` | LOW | Можно через news skill |

---

### 6. Onboarding (Онбординг)

**Из статьи:**
> Во время онбординга меня попросили заполнить инвестпрофиль, после чего предоставили 14-дневный Free Premium-доступ с ограничениями: 8 бесплатных сообщений в день и 1 активный слот мониторинга.

**Инвестпрофиль включает:**
- Опыт: beginner / intermediate / advanced
- Риск-толерантность: low / moderate / high
- Стратегия: long-only, swing, day-trading
- Интересующие рынки

**Агент использует профиль:**
> "I see you're an active trader with intermediate stock experience and moderate risk tolerance focusing on long-only strategies. I'll keep that in mind when developing setups for you."

---

### 6. Memory Intelligence

**Иерархия приоритетов:**
```
1. User Memory (персональные предпочтения)
2. Global Memory (общие знания)
3. General Guidelines (базовые правила)
```

**Когда использовать User Memory:**
- Риск-профиль пользователя
- История сделок
- Предпочтения по секторам
- Прошлые рецепты и результаты

---

### 7. Ticker Tracking

**Метаинструкция:**
> Я ДОЛЖЕН включать `<TICKERS>` теги в КАЖДОЕ сообщение.

**Правила:**
- Перед вызовом инструментов — короткое рассуждение (до 10 слов) + тикеры
- Извлечение релевантных тикеров из контекста

**Пример:**
```
AAPL торгуется на $178.50...
[анализ]
Это не является инвестиционной рекомендацией.
AAPL
```

---

## Системные инструкции (5 уровней)

### Уровень 1: Meta-правила и Идентичность

**professional_communication:**
- Тон: прямой, уверенный, основанный на данных
- Запрет на эмодзи в торговом анализе
- Профессиональный, но не чрезмерно формальный

**terminology:**
- Определения: Recipes, Monitoring Tasks
- Концептуальные взаимосвязи

**memory_intelligence:**
- Иерархия: User Memory → Global Memory → General Guidelines
- Когда использовать какую память

**data_freshness:**
- КРИТИЧНО: Всегда использовать инструменты для текущих данных
- Никогда не делать предположений о текущих ценах
- Когда вызывать экспертов-агентов

**code_execution_requirement:**
- Всегда использовать выполнение кода для опционных расчётов

---

### Уровень 2: Domain Logic

**recipe_philosophy:**
- Рецепты = живые документы, не статические заметки
- Эволюция через обновления, алерты, проверки

**monitoring_philosophy:**
- 3 типа: Templates, Scheduled Prompts, Custom Tasks
- Почему scheduled_prompt мощный

**options_formatting:**
- Всегда таблицы
- Обязательные колонки
- Workflow: данные → таблица → анализ

---

### Уровень 3: Safety & Compliance

**mandatory_disclaimer:**
- КАЖДЫЙ ответ: "Это не является инвестиционной рекомендацией"

**limitations:**
- Что я НЕ МОГУ делать
- Прозрачность об отсутствующих возможностях

**refusal_examples:**
- Конкретные примеры отказов (форекс, золото, крипто)

---

### Уровень 4: Output Layer

**auto_actions:**
- recipe_creation — когда автоматически создавать
- recipe_updates — когда обновлять
- monitoring_creation — когда создавать мониторинг

**response_format:**
- Структурированные ответы
- Таблицы для метрик

---

### Уровень 5: Context Input

**Динамические данные:**
- user_preferences — профиль трейдера
- monitoring_task_templates — доступные шаблоны
- week_economic_calendar — календарь недели
- conversation_recipes — активные рецепты
- conversation_monitoring_tasks — активные задачи
- current_market_time — время рынка

---

## Иерархия приоритетов

```
1. Legal Compliance (безопасность) — НАИВЫСШИЙ
   └─ Всегда дисклеймер
   └─ Жёсткие ограничения

2. Data Freshness (точность данных)
   └─ Всегда проверять текущие цены
   └─ Никаких предположений

3. Professional Communication (роль)
   └─ Тон Wall Street трейдера
   └─ Основано на данных

4. Domain Logic (функциональность)
   └─ Создание/обновление рецептов
   └─ Система мониторинга

5. Output Format (форма подачи)
   └─ Таблицы для опционов
   └─ Структурированные ответы
```

---

## Ключевые принципы дизайна

1. **Declarative > Imperative** — описание "что" важнее "как"
2. **Examples-driven** — множество примеров для сложных сценариев
3. **Fail-safe** — явные правила для edge cases
4. **Transparent** — честность об ограничениях
5. **Context-aware** — использование памяти пользователя

---

## Ограничения агента

### Финансовые:
- ❌ Давать инвестиционные советы
- ❌ Гарантировать прибыль
- ❌ Рекомендовать размеры позиций в валюте (только в %)

### Технические:
- ❌ Самостоятельно мониторить рынок в реальном времени (для этого monitoring tasks)
- ❌ Торговать от имени пользователя
- ❌ Форекс (пока нет инструментов)
- ❌ Криптовалюты (поддержка в будущем)

### Общие:
- ❌ Отвечать на вопросы не о финансах/трейдинге
- ❌ Предсказывать будущее с уверенностью
- ❌ Действовать вопреки интересам пользователя

---

## Что МОЖЕТ агент

- ✅ Анализировать рынок с реальными данными
- ✅ Создавать торговые идеи и рецепты
- ✅ Настраивать алерты и мониторинг
- ✅ Помогать с риск-менеджментом
- ✅ Объяснять стратегии и сетапы
