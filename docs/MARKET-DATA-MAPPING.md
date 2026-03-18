# Market Data Tools Mapping

Соответствие инструментов Stonki AI и T-Invest Agent.

---

## 1. get_most_recent_bar → moex security:trade-data

### Stonki AI
```yaml
symbol_list: ["AAPL", "MSFT"]
bar_size: 1, 5, 15
bar_unit: "Second" | "Minute" | "Hour" | "Daily" | "Weekly" | "Monthly"
```

**Возвращает:** OHLCV (open, high, low, close, volume, vwap)

### T-Invest Agent (текущее)
```bash
moex security:trade-data SBER
```

**Возвращает:** Last, Open/High/Low, Volume Today

### TODO: Расширение
```bash
# Добавить параметры для исторических данных
moex security:trade-data SBER --bar-size=5 --bar-unit=Minute --limit=100
moex security:trade-data SBER --bar-size=1 --bar-unit=Hour --from=2025-03-01

# Множественные тикеры
moex security:trade-data SBER,GAZP,LKOH
```

---

## 2. get_option_chain → [TODO]

### Stonki AI
```yaml
symbol: "AAPL"
strike_price_gte: 150
strike_price_lte: 200
expiration_date: "2025-04-15"       # точная дата
expiration_date_gte: "2025-03-01"   # от
expiration_date_lte: "2025-06-01"   # до
option_type: "call" | "put"
limit: 100
```

**Возвращает:** TSV с ценами, греками, IV, объёмом

### T-Invest Agent (TODO)
```bash
# Требует интеграции с MOEX FORTS
moex options:chain SBER --expiration=2025-06
moex options:chain SBER --strike-min=200 --strike-max=300
moex options:chain SBER --type=call

# Вывод:
# | Strike | Bid | Ask | IV | Delta | Gamma | Theta | Vega | Volume |
# |--------|-----|-----|----|-------|-------|-------|------|--------|
# | 250    | ... | ... | .. | ...   | ...   | ...   | ...  | ...    |
```

**Примечание:** На MOEX опционы менее ликвидны чем на US рынках. Приоритет LOW.

---

## 3. get_ticker_details → moex security:specification

### Stonki AI
```yaml
symbol: "AAPL"
```

**Возвращает:** Детальная информация о компании

### T-Invest Agent
```bash
moex security:specification SBER
```

**Возвращает:** ISIN, List Level, Type, Issue Size

### TODO: Расширение
```bash
# Добавить больше данных компании
moex security:details SBER --full

# Включить:
# - Сектор/индустрия
# - Рыночная капитализация
# - Free float
# - Дивидендная история
# - Основные акционеры
```

---

## 4. get_symbol_summaries → skill analyze:quick

### Stonki AI
```yaml
symbols: "AAPL,MSFT,TSLA"
```

**Возвращает:** CSV с метриками:
- Performance (доходность)
- Fundamentals (P/E, P/B, ROE)
- Earnings (дата отчётности)
- Technical indicators

### T-Invest Agent
```bash
skill analyze:quick --ticker=SBER
```

**Возвращает:** Сводка технического + фундаментального

### TODO: Расширение
```bash
# Пакетный режим
skill analyze:quick --tickers=SBER,GAZP,LKOH

# Формат CSV
skill analyze:quick --ticker=SBER --format=csv

# Выборочные метрики
skill analyze:quick --ticker=SBER --metrics=pe,pb,roe,div_yield
```

**Структура вывода:**
```
| Ticker | Price | P/E | P/B | ROE | Div% | RSI | Trend | Score |
|--------|-------|-----|-----|-----|------|-----|-------|-------|
| SBER   | 255   | 5.2 | 0.8 | 22% | 12%  | 45  | →     | 8/10  |
| GAZP   | 130   | 3.1 | 0.4 | 15% | 8%   | 52  | ↑     | 7/10  |
```

---

## 5. get_week_earnings_events → calendar earnings

### Stonki AI
```yaml
country: "US"
limit: 10
offset: 0
min_market_cap: 1000000000
tickers: ["AAPL", "MSFT"]
```

**Возвращает:** Календарь отчётностей на неделю

### T-Invest Agent (TODO)
```bash
calendar earnings --period=week
calendar earnings --ticker=SBER
calendar earnings --sector=financial --period=month
calendar earnings --min-market-cap=1000000000

# Вывод:
# | Date       | Ticker | Company    | Period | Expected | Time   |
# |------------|--------|------------|--------|----------|--------|
# | 2025-03-20 | SBER   | Сбербанк   | Q4     | EPS      | 09:00  |
# | 2025-03-22 | LKOH   | Лукойл     | Q4     | EPS      | 18:00  |
```

**Источники данных:**
- MOEX ISS API (календарь событий)
- Компания (пресс-релизы)
- Ручное добавление

---

## Итоговая таблица соответствий

| Stonki AI | T-Invest Agent | Статус | Приоритет |
|-----------|----------------|--------|-----------|
| `get_most_recent_bar` | `moex security:trade-data` | ✅ Базовый | HIGH |
| `get_most_recent_bar` (multi-ticker, history) | — | ❌ TODO | MEDIUM |
| `get_option_chain` | — | ❌ TODO | LOW |
| `get_ticker_details` | `moex security:specification` | ✅ Базовый | HIGH |
| `get_ticker_details` (extended) | — | ❌ TODO | LOW |
| `get_symbol_summaries` | `skill analyze:quick` | ✅ Базовый | HIGH |
| `get_symbol_summaries` (batch, csv) | — | ❌ TODO | MEDIUM |
| `get_week_earnings_events` | `calendar earnings` | ❌ TODO | MEDIUM |

---

## Приоритеты доработки

### HIGH (Phase 1)
1. Расширить `moex security:trade-data` параметрами bar-size/unit
2. Добавить пакетный режим в `skill analyze:quick`

### MEDIUM (Phase 2)
1. Реализовать `calendar earnings`
2. Добавить исторические OHLCV данные
3. CSV формат для `skill analyze:quick`

### LOW (Phase 3)
1. Опционы (если есть спрос)
2. Расширенные данные компании
