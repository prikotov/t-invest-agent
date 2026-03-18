# Примеры рецептов и мониторинга

Полный каталог сценариев создания рецептов и задач мониторинга.

---

## 10+ примеров рецептов

### 1. Long акция с техническим обоснованием

```yaml
id: long-sber-breakout
ticker: SBER
direction: LONG
status: WATCHING

thesis: |
  Пробой сопротивления 270 на повышенном объёме.
  Цель — исторический максимум 300.

context: |
  - SBER консолидировался 3 недели в диапазоне 250-270
  - Вчера объём в 2 раза выше среднего
  - RSI 60 — есть запас для роста
  - Фундаментал: P/E 5.2, дивиденды 12%

entry:
  zone: [268, 272]
  type: limit или пробой 270 на объёме

target:
  price: 300
  reason: "Исторический максимум, психологический уровень"
  percent: +11%

stop:
  price: 255
  reason: "Ниже поддержки, ложный пробой"
  percent: -5%

risk_reward: 2.2:1

position_size:
  recommendation: "2-3% портфеля"
  risk_per_trade: "1%"

monitoring:
  - type: price_cross
    level: 270
    action: "Уведомление о пробое"
  - type: price_cross
    level: 300
    action: "Цель достигнута"
  - type: price_cross
    level: 255
    action: "Стоп сработал"

created: 2024-01-15
updated: 2024-01-15
```

### 2. Short акция на слабых отчётах

```yaml
id: short-mgnt-earnings
ticker: MGNT
direction: SHORT
status: WATCHING

thesis: |
  Магнит отчитался хуже ожиданий, downguidance на год.
  Техника — пробой поддержки 550.

context: |
  - EPS ниже консенсуса на 15%
  - SSSG отрицательный 3-й квартал подряд
  - Руководство снизило прогноз по EBITDA
  - Техника: пробой восходящего тренда

entry:
  zone: [545, 555]
  type: пробой 550 с подтверждением

target:
  price: 480
  reason: "Следующая поддержка, уровни февраля"
  percent: -12%

stop:
  price: 575
  reason: "Выше сопротивления, ложный пробой"
  percent: +4%

risk_reward: 3.0:1

monitoring:
  - type: price_cross
    level: 550
  - type: recurring_prompt
    schedule: daily
    prompt: "Проверить новости MGNT за 24ч"
```

### 3. Дивидендная игра

```yaml
id: div-play-gmkn
ticker: GMKN
direction: LONG
status: WATCHING

thesis: |
  Норникель перед дивидендной отсечкой.
  Исторически растёт за 2 недели до отсечки.

context: |
  - Дивиденды ожидаются 1800+ руб на акцию
  - Yield ~10% к текущей цене
  - Цены на никель стабильны
  - Отсечка через 18 дней

entry:
  zone: [17500, 18000]
  type: limit

target:
  price: 19500
  reason: "Дивидендный + преддивидендный рост"
  percent: +11%

stop:
  price: 16800
  reason: "Ниже поддержки 50-дневной MA"
  percent: -4%

risk_reward: 2.75:1

special_notes: |
  - Закрыть позицию за 1 день до отсечки (T+1)
  - Или держать до отсечки для получения дивидендов
```

### 4. Парный трейд (pairs trade)

```yaml
id: pair-lkoh-rosn
tickers: [LKOH, ROSN]
direction: LONG LKOH / SHORT ROSN
status: WATCHING

thesis: |
  Спред LKOH/ROSN сузился до исторического минимума.
  Лукойл премиальный, Роснефть дисконтный — вернётся к среднему.

context: |
  - Спред сейчас 0.8, историческое среднее 1.2
  - Обе компании имеют схожий профиль (нефть)
  - Лукойл: лучший гovernance, стабильные дивиденды
  - Роснефть: геополитический дисконт, неопределённость

entry:
  spread: 0.8
  action: "Купить LKOH, продать ROSN (равные веса по бете)"

target:
  spread: 1.1
  reason: "Возврат к среднему"

stop:
  spread: 0.65
  reason: "Спред продолжает сужаться"

risk_reward: 2.0:1

monitoring:
  - type: recurring_prompt
    schedule: daily
    prompt: "Рассчитать текущий спред LKOH/ROSN"
```

### 5. Секторная ставка (ETF/индекс)

```yaml
id: sector-banks-long
tickers: [SBER, TCSG, VTBR]
direction: LONG
status: WATCHING

thesis: |
  Банковский сектор РФ недооценён на фоне роста ставок.
  Высокая маржинальность, чистые NIM.

context: |
  - Ключевая ставка ЦБ высокая — банки зарабатывают
  - SBER: P/E 5.2, ROE 25%
  - TCSG: рост клиентской базы 30% YoY
  - VTBR: недооценка к SBER исторически

entry:
  allocation:
    SBER: 50%
    TCSG: 30%
    VTBR: 20%
  timing: "По уровням, не сразу"

target:
  return: +20%
  horizon: "6-12 месяцев"

stop:
  return: -10%
  reason: "Секторная ставка не оправдалась"

monitoring:
  - type: recurring_prompt
    schedule: weekly
    prompt: "Проверить новости банковского сектора, решения ЦБ"
```

### 6. Swing-trade на отчёте

```yaml
id: swing-tcsg-earnings
ticker: TCSG
direction: LONG
status: WAITING_EARNINGS

thesis: |
  Тинькофф отчитывается 25-го.
  Исторически сильные отчёты, рост на 5-10% после.

context: |
  - Консенсус: EPS рост 40% YoY
  - Активные клиенты +25%
  - Объём кредитования +35%
  - Техника: аккумулирование перед отчётом

entry:
  zone: [2800, 2900]
  timing: "За 2-3 дня до отчёта"

target:
  price: 3200
  reason: "Пост-earnings rally"
  percent: +10%

stop:
  price: 2650
  reason: "Отчёт разочаровал"
  percent: -7%

risk_reward: 1.4:1

risk_warning: "Earnings play — повышенный риск волатильности"
```

### 7. Mean reversion (возврат к среднему)

```yaml
id: mean-reversion-alrs
ticker: ALRS
direction: LONG
status: WATCHING

thesis: |
  Алроса упала на 15% за неделю без фундаментальных причин.
  RSI < 25 — перепроданность, возврат к среднему.

context: |
  - Новостей нет — падение на объёмах
  - Возможно крупный продавец вышел
  - Алмазный рынок стабильный
  - Дивиденды 8%+

entry:
  zone: [65, 68]
  type: "Первые признаки стабилизации"

target:
  price: 78
  reason: "5-дневная MA, возврат к норме"
  percent: +15%

stop:
  price: 60
  reason: "Продолжение падения"
  percent: -8%

risk_reward: 1.9:1

monitoring:
  - type: technical_signal
    indicator: RSI
    condition: "RSI > 30"
    action: "Сигнал разворота"
```

### 8. Value инвестиция (долгосрочная)

```yaml
id: value-gazp
ticker: GAZP
direction: LONG
status: ACCUMULATING

thesis: |
  Газпром торгуется на уровне 2019 года при выросших ценах на газ.
  Мультипликаторы исторически низкие, дивиденды восстановлены.

context: |
  - P/E 3.5 — исторический минимум
  - P/B 0.4 — торгуется ниже балансовой
  - Дивиденды восстановлены после паузы
  - Газ в Азию — долгосрочный тренд

entry:
  zone: [150, 170]
  strategy: "Накопление частями в течение 3 месяцев"

target:
  price: 250
  horizon: "12-24 месяца"
  reason: "Нормализация мультипликаторов"

stop:
  price: 130
  reason: "Структурные проблемы, новые санкции"

position_size:
  recommendation: "5-8% портфеля"
  rationale: "Value play, долгосрочный горизонт"

monitoring:
  - type: recurring_prompt
    schedule: monthly
    prompt: "Пересмотреть тезис GAZP: цены на газ, санкции, отчёты"
```

### 9. Momentum trade

```yaml
id: momentum-fixp
ticker: FIXP
direction: LONG
status: WATCHING

thesis: |
  Fix Price показал сильный отчёт, гэп вверх на 12%.
  Momentum продолжится на объёмах.

context: |
  - EBITDA +25% YoY, выше консенсуса
  - SSSG положительный впервые за 2 года
  - Объём в 5 раз выше среднего
  - Инсайдеры покупают

entry:
  zone: [42, 44]
  type: "Pullback к уровню гэпа"

target:
  price: 52
  reason: "Предыдущее сопротивление, +18% от входа"
  percent: +18%

stop:
  price: 39
  reason: "Ниже гэпа, momentum угас"
  percent: -7%

risk_reward: 2.6:1

monitoring:
  - type: recurring_prompt
    schedule: daily
    prompt: "Проверить объёмы FIXP, следить за угасанием momentum"
```

### 10. Event-driven (M&A, реструктуризация)

```yaml
id: event-yndx-split
ticker: YNDX
direction: LONG
status: WATCHING

thesis: |
  Яндекс проводит реструктуризацию, выделение бизнеса.
  После завершения — потенциальный upside от sum-of-parts.

context: |
  - Сплан/выделение международных активов
  - Российский бизнес недооценён из-за геополитики
  - После реструктуризации — чистый российский play
  - Дата завершения: Q2 2024

entry:
  zone: [2400, 2600]
  timing: "До завершения реструктуризации"

target:
  price: 3200
  reason: "Re-rating после clarity по структуре"
  percent: +23%

stop:
  price: 2200
  reason: "Задержка реструктуризации, новые риски"
  percent: -8%

risk_reward: 2.9:1

risk_warning: "Event-driven — зависит от регуляторных решений"

monitoring:
  - type: recurring_prompt
    schedule: weekly
    prompt: "Проверить новости YNDX по реструктуризации"
```

### 11. Covered Call (опционная стратегия)

```yaml
id: covered-call-sber
ticker: SBER
direction: LONG stock + SHORT call
status: ACTIVE

thesis: |
  SBER в боковике 250-270.
  Продажа covered call для генерации дохода.

context: |
  - Акция: 1000 шт SBER @ 260
  - Волатильность высокая — премии хорошие
  - Горизонт: 30 дней

structure:
  long_stock:
    shares: 1000
    avg_price: 260
    value: 260000

  short_call:
    strike: 280
    premium: 8
    expiry: 30 days
    contracts: 10 (1000 shares)
    premium_received: 8000

outcomes:
  if_below_280:
    result: "Keep premium + stock"
    return: "8000 / 260000 = 3.1% = 37% annualized"

  if_above_280:
    result: "Stock called away at 280"
    total_return: "(280-260)*1000 + 8000 = 28000"
    percent: "+10.8%"

  breakeven: 252

risk:
  opportunity_cost: "Если SBER > 280, теряешь upside"

monitoring:
  - type: price_cross
    level: 280
    action: "Уведомление: возможно assignment"
  - type: recurring_prompt
    schedule: weekly
    prompt: "Оценить rolling call или закрытие"
```

---

## Блок-схемы мониторинга

### Когда какой тип мониторинга использовать

```
                    ┌─────────────────────────────────────┐
                    │  ЧТО НУЖНО ОТСЛЕЖИВАТЬ?             │
                    └─────────────────────────────────────┘
                                    │
            ┌───────────────────────┼───────────────────────┐
            │                       │                       │
            ▼                       ▼                       ▼
    ┌───────────────┐      ┌───────────────┐      ┌───────────────┐
    │ Ценовой       │      │ Технический   │      │ Периодический │
    │ уровень       │      │ сигнал        │      │ анализ        │
    └───────────────┘      └───────────────┘      └───────────────┘
            │                       │                       │
            ▼                       ▼                       ▼
    ┌───────────────┐      ┌───────────────┐      ┌───────────────┐
    │ price_cross   │      │ technical_    │      │ recurring_    │
    │               │      │ signal        │      │ prompt        │
    └───────────────┘      └───────────────┘      └───────────────┘
            │                       │                       │
            ▼                       ▼                       ▼
    ┌───────────────┐      ┌───────────────┐      ┌───────────────┐
    │ Примеры:      │      │ Примеры:      │      │ Примеры:      │
    │ • Цена > 300  │      │ • RSI > 70    │      │ • Каждое утро │
    │ • Цена < 250  │      │ • MACD cross  │      │ • Каждую нед. │
    │ • Уровень вх. │      │ • MA cross    │      │ • Перед отчё. │
    └───────────────┘      └───────────────┘      └───────────────┘
```

### Детальная блок-схема выбора

```markdown
## Алгоритм выбора типа мониторинга

1. НУЖЕН ЛИ АВТОМАТИЧЕСКИЙ АЛЕРТ ПРИ УСЛОВИИ?
   │
   ├─ ДА, при достижении цены → price_cross
   │   └─ Пример: "Сообщи когда SBER будет 260"
   │
   ├─ ДА, при техническом сигнале → technical_signal
   │   └─ Пример: "Сообщи когда RSI Сбера > 70"
   │
   └─ НЕТ, нужна регулярная проверка → recurring_prompt
       └─ Пример: "Каждое утро проверяй новости по портфелю"

2. КАК ЧАСТО НУЖНА ПРОВЕРКА?
   │
   ├─ Один раз при событии → price_cross / technical_signal
   │
   └─ Регулярно → recurring_prompt
       ├─ Ежедневно (утро/вечер)
       ├─ Еженедельно (понедельник/пятница)
       ├─ Перед событием (отчёт, отсечка)
       └─ По расписанию cron

3. ЧТО ДОЛЖЕН ДЕЛАТЬ АГЕНТ ПРИ СРАБАТЫВАНИИ?
   │
   ├─ Просто уведомить → базовый alert
   │
   └─ Выполнить анализ → prompt с инструкциями
       └─ Пример: "При срабатывании проверь новости,
                    обнови рецепт, предложи действие"
```

### Примеры recurring_prompt

```yaml
# Ежеутренняя проверка
id: morning-check
type: recurring_prompt
schedule: "0 9 * * 1-5"  # 09:00 пн-пт
prompt: |
  1. Получить portfolio:positions
  2. Для каждой позиции > 5% веса:
     - news news:fetch --ticker=$TICKER --days=1
     - moex security:trade-data $TICKER
  3. Проверить активные рецепты:
     - Цена достигла уровня входа?
     - Цена достигла цели/стопа?
  4. Сформировать отчёт:
     - Изменения за ночь
     - Важные новости
     - Срабатывания алертов

---

# Еженедельный отчёт
id: weekly-report
type: recurring_prompt
schedule: "0 18 * * 5"  # 18:00 пятница
prompt: |
  1. portfolio:report --period=week
  2. Для позиций с изменением > 5%:
     - Причина изменения
     - skill analyze:technical
  3. Рекомендации на следующую неделю

---

# Проверка перед отчётом
id: pre-earnings-check
type: recurring_prompt
schedule: "0 8 * * *"  # Каждое утро
trigger_condition: "Есть ли отчёты на этой неделе?"
prompt: |
  1. Проверить calendar:earnings на неделю
  2. Для тикеров с отчётами в ближайшие 3 дня:
     - Текущая цена vs ожидания
     - Историческая реакция на отчёты
     - Рекомендация: держать/закрыть/хеджировать
```

### Lifecycle мониторинга

```
┌─────────────────────────────────────────────────────────────────┐
│  СОЗДАНИЕ                                                       │
│  ─────────                                                      │
│  monitor create price --ticker=SBER --level=260                 │
│  или автоматически при создании рецепта                         │
└─────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│  ACTIVE                                                         │
│  ───────                                                        │
│  Мониторинг работает, проверяет условие                         │
│                                                                 │
│  Возможные действия:                                            │
│  • pause — временно остановить                                  │
│  • update — изменить параметры                                  │
└─────────────────────────────────────────────────────────────────┘
                               │
              ┌────────────────┴────────────────┐
              │                                 │
              ▼                                 ▼
┌─────────────────────────┐      ┌─────────────────────────────────┐
│  TRIGGERED              │      │  PAUSED                         │
│  ─────────              │      │  ──────                         │
│  Условие выполнено      │      │  Временно остановлен            │
│  → Уведомление          │      │  → resume для возобновления     │
│  → Автоматические дейст.│      │                                 │
└─────────────────────────┘      └─────────────────────────────────┘
              │                                 │
              ▼                                 │
┌─────────────────────────┐                     │
│  COMPLETED / ARCHIVED   │◄────────────────────┘
│  ───────────────────    │
│  Задача выполнена       │
│  или больше не актуальна│
└─────────────────────────┘
```

---

## Шаблоны промптов для мониторинга

### price_cross

```markdown
## Создание

monitor create price \
  --ticker=SBER \
  --level=260 \
  --direction=above \
  --message="SBER достиг 260 ₽ — уровень входа в рецепте long-sber-breakout"

## При срабатывании агент выполняет:

1. Уведомление пользователю
2. Проверка связанных рецептов
3. Получение актуальных данных:
   - moex security:trade-data SBER
   - news news:fetch --ticker SBER --days=1
4. Рекомендация действия
```

### technical_signal

```markdown
## Создание

monitor create technical \
  --ticker=SBER \
  --indicator=RSI \
  --condition="greater_than" \
  --value=70 \
  --message="RSI SBER > 70 — перекупленность"

## При срабатывании:

1. skill analyze:technical --ticker=SBER
2. Оценка: разворот или продолжение тренда?
3. Рекомендация: держать/продать/хеджировать
```

### recurring_prompt (ежедневный)

```markdown
## Создание

monitor create schedule \
  --cron="0 9 * * 1-5" \
  --name="morning-portfolio-check" \
  --prompt="@morning-check-prompt"

## Шаблон промпта:

Ты — T-Invest Agent. Ежеутренняя проверка портфеля.

1. Получить текущие позиции: portfolio:positions
2. Для каждой позиции:
   - Текущая цена: moex security:trade-data
   - Новостей за ночь: news news:fetch --days=1
3. Проверить активные рецепты:
   - Есть срабатывания уровней?
   - Нужно обновление рецепта?
4. Сформировать краткий отчёт:
   - Изменения цен (%)
   - Важные новости (ссылки)
   - Рекомендуемые действия

Формат: компактный, без лишних слов.
Завершить: "Не является инвестиционной рекомендацией."
```

### recurring_prompt (перед событием)

```markdown
## Создание

monitor create schedule \
  --cron="0 8 * * *" \
  --name="earnings-watch" \
  --prompt="@earnings-check-prompt" \
  --condition="Есть отчёты на этой неделе"

## Шаблон промпта:

Проверить календарь отчётов на текущую неделю.

Для каждого тикера с отчётом в ближайшие 3 дня:

1. Текущая цена: moex security:trade-data
2. Ожидания: consensus EPS, revenue
3. Историческая реакция: как реагировал в прошлые разы
4. Текущая позиция: есть ли в портфеле?

Рекомендация:
- Держать через отчёт
- Закрыть до отчёта
- Хеджировать (options)

Если позиция в портфеле и отчёт завтра — ОБЯЗАТЕЛЬНОЕ уведомление.
```

---

## Интеграция рецептов и мониторинга

### Автоматическое создание мониторинга при рецепте

```markdown
## Правило

При создании рецепта АВТОМАТИЧЕСКИ создавать мониторинг:

1. recipe.entry.zone → price_cross на нижнюю и верхнюю границу
2. recipe.target.price → price_cross
3. recipe.stop.price → price_cross

## Пример

Рецепт: long-sber-breakout
- Entry: [268, 272]
- Target: 300
- Stop: 255

Автоматически создаются:
- monitor price SBER 268 (вход)
- monitor price SBER 272 (вход верх)
- monitor price SBER 300 (цель)
- monitor price SBER 255 (стоп)
```

### Обновление рецепта при срабатывании мониторинга

```markdown
## При срабатывании price_cross на уровень входа:

1. recipe.status → ACTIVE (позиция открыта)
2. recipe.actual_entry → текущая цена
3. recipe.updated → timestamp

## При срабатывании на цель:

1. recipe.status → COMPLETED
2. recipe.exit_reason → "target_reached"
3. recipe.actual_exit → текущая цена
4. recipe.pnl → расчёт прибыли

## При срабатывании на стоп:

1. recipe.status → STOPPED
2. recipe.exit_reason → "stop_loss"
3. recipe.actual_exit → текущая цена
4. recipe.pnl → расчёт убытка
```

---

## CLI команды для работы с рецептами и мониторингом

```bash
# Рецепты
recipe create --ticker=SBER --direction=LONG --entry=260 --target=300 --stop=245
recipe list
recipe show long-sber-breakout
recipe update long-sber-breakout --status=ACTIVE
recipe archive long-sber-breakout

# Мониторинг
monitor create price --ticker=SBER --level=260
monitor create technical --ticker=SBER --indicator=RSI --value=70
monitor create schedule --cron="0 9 * * 1-5" --prompt="@morning-check"
monitor list
monitor pause morning-portfolio-check
monitor resume morning-portfolio-check
monitor delete long-sber-breakout-alert-1
```
