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

---

## 12. Backtesting — HIGH PRIORITY (NEW)

### Концепция
Тестирование торговых стратегий на исторических данных MOEX.

### Что нужно реализовать

#### 12.1 CLI команды
```bash
# Бэктест стратегии
backtest run --strategy=dividend --start=2023-01-01 --end=2024-01-01
backtest run --strategy=mean_reversion --ticker=SBER

# Результаты
backtest report --id=UUID
backtest compare --ids=uuid1,uuid2

# Исторические данные
backtest fetch --ticker=SBER --start=2023-01-01 --interval=1d
```

#### 12.2 Метрики бэктеста
- Total Return
- Sharpe Ratio
- Max Drawdown
- Win Rate
- Profit Factor
- CAGR

---

## 13. Portfolio Optimization — MEDIUM PRIORITY (NEW)

### Концепция
Оптимизация портфеля по теории Марковица и другим моделям.

### Что нужно реализовать

#### 13.1 CLI команды
```bash
# Оптимизация по Шарпу
optimize sharpe --risk-free=0.12

# Минимизация риска при заданной доходности
optimize min-risk --target-return=0.15

# Efficient Frontier
optimize frontier --points=20

# Ребалансировка к оптимуму
optimize rebalance --to=optimal
```

#### 13.2 Модели оптимизации
- Mean-Variance (Markowitz)
- Risk Parity
- Black-Litterman
- Hierarchical Risk Parity (HRP)

---

## 14. Risk Metrics — MEDIUM PRIORITY (NEW)

### Концепция
Расчёт метрик риска портфеля.

### Что нужно реализовать

#### 14.1 CLI команды
```bash
# VaR (Value at Risk)
risk var --confidence=0.95 --horizon=1d
risk var --method=historical
risk var --method=parametric
risk var --method=monte-carlo

# CVaR (Conditional VaR / Expected Shortfall)
risk cvar --confidence=0.95

# Max Drawdown
risk max-drawdown --period=year

# Stress Test
risk stress --scenario=2008_crisis
risk stress --scenario=rate_hike --pct=3

# Beta, Correlation
risk beta --ticker=SBER --benchmark=IMOEX
risk correlation --tickers=SBER,LKOH,GAZP
```

#### 14.2 Пример вывода
```
Portfolio Risk Metrics
─────────────────────────────────────
VaR (95%, 1d):      -2.3% (₽23,000)
CVaR (95%):         -3.1% (₽31,000)
Max Drawdown:       -15.2% (Mar 2023)
Beta vs IMOEX:      0.85
Volatility (ann):   18.5%
─────────────────────────────────────
```

---

## 15. Correlation Matrix — LOW PRIORITY (NEW)

### Концепция
Анализ корреляций между активами портфеля.

### Что нужно реализовать

#### 15.1 CLI команды
```bash
# Матрица корреляций
correlation matrix --period=year
correlation matrix --tickers=SBER,LKOH,GAZP,GMKN

# Кластеризация активов
correlation cluster --method=hierarchical

# Диверсификация
correlation diversification-score
```

---

## 16. Tax Calculator — LOW PRIORITY (NEW)

### Концепция
Расчёт налоговых обязательств и tax-loss harvesting.

### Что нужно реализовать

#### 16.1 CLI команды
```bash
# Расчёт НДФЛ
tax calculate --year=2024
tax calculate --method=FIFO
tax calculate --method=LIFO

# Tax-loss harvesting возможности
tax harvesting-opportunities --min-loss=10000

# Дивидендный налог
tax dividends --year=2024

# Отчёт для декларации
tax report --year=2024 --format=pdf
```

---

## 17. Multi-Account — LOW PRIORITY (NEW)

### Концепция
Поддержка нескольких брокерских счетов.

### Что нужно реализовать

#### 17.1 CLI команды
```bash
# Управление счетами
account list
account add --name=ИИС --id=12345
account switch --name=ИИС
account aggregate  # Сводный портфель

# Отчёты по счёту
portfolio:report --account=ИИС
portfolio:analyze --account=all
```

---

## 18. Export Reports — MEDIUM PRIORITY (NEW)

### Концепция
Экспорт отчётов в PDF/Excel для архива.

### Что нужно реализовать

#### 18.1 CLI команды
```bash
# Экспорт
export report --period=month --format=pdf
export report --period=year --format=excel
export portfolio --format=csv
export trades --year=2024 --format=excel

# Шаблоны
export template --name=monthly_brief
export template --name=tax_report
```

---

## 19. Telegram Bot — HIGH PRIORITY (NEW)

### Концепция
Полноценный Telegram бот для интерактивного взаимодействия.

### Что нужно реализовать

#### 19.1 Команды бота
```
/start         - Начало работы
/portfolio     - Портфель
/alert SBER 260 - Создать алерт
/news SBER     - Новости
/analyze SBER  - Анализ тикера
/recipes       - Активные рецепты
/weekly        - Еженедельный отчёт
```

#### 19.2 Push-уведомления
- Срабатывание алертов
- Важные новости по позициям
- Напоминания о scheduled отчётах

---

## 20. Web Dashboard — LOW PRIORITY (NEW)

### Концепция
Веб-интерфейс для мониторинга портфеля.

### Что нужно реализовать

#### 20.1 Стек
- Frontend: React/Vue
- Backend: PHP API или Node.js
- Charts: TradingView lightweight charts

#### 20.2 Функции
- Dashboard с портфелем
- Графики цен
- Активные рецепты
- История сделок
- Настройки уведомлений

---

## 21. REST API — MEDIUM PRIORITY (NEW)

### Концепция
API для интеграции с другими системами.

### Что нужно реализовать

#### 21.1 Эндпоинты
```
GET  /api/portfolio           - Портфель
GET  /api/portfolio/positions - Позиции
GET  /api/tickers/{ticker}    - Данные тикера
GET  /api/news?ticker=SBER    - Новости
POST /api/recipes             - Создать рецепт
GET  /api/recipes             - Список рецептов
POST /api/monitors            - Создать алерт
GET  /api/monitors            - Список алертов
```

#### 21.2 Аутентификация
- API Key
- JWT токены

---

## 22. AI Insights — MEDIUM PRIORITY (NEW)

### Концепция
Автоматические инсайты на основе паттернов в портфеле.

### Что нужно реализовать

#### 22.1 Типы инсайтов
- **Sector Concentration**: "40% портфеля в нефтегазе"
- **Dividend Gap**: "Дивидендная доходность ниже рынка"
- **Risk Alert**: "Высокая корреляция между SBER и LKOH"
- **Opportunity**: "SBER просел на 10%, RSI < 30"
- **Rebalance Suggestion**: "SBER вырос до 25% веса"

#### 22.2 CLI команды
```bash
# Сгенерировать инсайты
insights generate
insights generate --type=risk
insights generate --type=opportunity

# История инсайтов
insights history --days=30
```

---

## Обновлённые приоритеты

### Phase 1: Foundation (2-3 недели)
- [ ] Recipe skill
- [ ] Monitor skill (демон)
- [ ] Memory skill
- [ ] Telegram Bot (уведомления)

### Phase 2: Analytics (2-3 недели)
- [ ] Calc skill
- [ ] Risk Metrics (VaR, Drawdown)
- [ ] Correlation Matrix
- [ ] Backtesting (базовый)

### Phase 3: Optimization (1-2 недели)
- [ ] Portfolio Optimization (Markowitz)
- [ ] AI Insights
- [ ] Export Reports

### Phase 4: Integration (опционально)
- [ ] REST API
- [ ] Web Dashboard
- [ ] Multi-account
- [ ] Tax Calculator
