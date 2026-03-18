# T-Invest Agent Roadmap

Анализ возможностей на основе исследования Stonki AI и текущего состояния проекта.

## Сравнение с Stonki AI

### Что уже реализовано

| Stonki AI | T-Invest Agent | Статус |
|-----------|----------------|--------|
| `get_most_recent_bar` | `moex security:trade-data` | ✅ Базовый |
| `get_ticker_details` | `moex security:specification` | ✅ |
| `get_symbol_summaries` | `skill analyze:quick` | ✅ |
| `transfer_to_news_agent` | `news news:fetch` | ✅ |
| Технический анализ | `skill analyze:technical` | ✅ |
| Фундаментальный анализ | `skill analyze:fundamental` | ✅ |
| Скрининг акций | `skill screen:stocks` | ✅ |

### Чего не хватает

| Stonki AI | Описание | Приоритет |
|-----------|----------|-----------|
| **Recipes** | Живые торговые журналы с отслеживанием сетапов | HIGH |
| **Monitoring Tasks** | Алерты и scheduled проверки 24/7 | HIGH |
| **User Memory Agent** | Память о предпочтениях и истории сделок | HIGH |
| **get_most_recent_bar (history)** | Исторические OHLCV с bar_size/unit | MEDIUM |
| **get_most_recent_bar (multi)** | Множественные тикеры за раз | MEDIUM |
| **Scanner Agent** | Сканирование по тех/фундам критериям | MEDIUM |
| **Code Execution** | Python для сложных расчётов | MEDIUM |
| **get_week_earnings_events** | Календарь отчётностей | MEDIUM |
| **get_symbol_summaries (batch)** | Пакетный режим с CSV выводом | MEDIUM |
| **Social Media Agent** | Анализ настроений: Telegram, Smart-Lab, Banki.ru | MEDIUM |
| **Options Chain** | Опционные данные, греки, IV | LOW |
| **Web Search Agent** | Поиск в интернете (Яндекс, Google) | LOW |

---

## Приоритетные направления

### 1. Recipes (Рецепты) - HIGH

**Концепция из Stonki AI:**
- Живые торговые журналы, которые эволюционируют с рынком
- Отслеживание: тезис → вход → выход → результат
- Автоматические обновления при рыночных событиях

**Реализация для T-Invest Agent:**

```
recipe/
├── create --ticker=SBER --thesis="Дивидендная история"
├── update --id=UUID --note="Цена достигла уровня входа"
├── list --status=active
├── close --id=UUID --result=+15%
└── history --ticker=SBER
```

**Структура рецепта:**
```json
{
  "id": "uuid",
  "ticker": "SBER",
  "thesis": "Дивидендный доход 12%+",
  "entry_zone": [250, 260],
  "target": 300,
  "stop": 240,
  "position_size": "5% портфеля",
  "risk_reward": "2:1",
  "status": "active",
  "created_at": "2025-03-18",
  "updates": [
    {"date": "2025-03-20", "note": "Цена 255, частичный вход"}
  ]
}
```

**Промптинг для создания рецептов:**
```
Когда пользователь выражает торговую идею:
1. Извлечь: тикер, тезис, уровни входа/выхода
2. Рассчитать risk/reward на основе текущих цен (moex security:trade-data)
3. Проверить фундаментальные метрики (skill analyze:fundamental)
4. Создать рецепт с отслеживанием
```

---

### 2. Monitoring Tasks (Мониторинг) - HIGH

**Концепция из Stonki AI:**
3 типа мониторинга:
1. **price_cross** - алерт при пересечении уровня
2. **iv_cross** - алерт при изменении волатильности
3. **recurring_prompt** - запланированные проверки

**Реализация для T-Invest Agent:**

```
monitor/
├── create --ticker=SBER --type=price --level=260
├── create --ticker=SBER --type=schedule --cron="0 9 * * 1-5"
├── list
├── pause --id=UUID
├── resume --id=UUID
└── delete --id=UUID
```

**Типы алертов:**
```yaml
# price_cross
ticker: SBER
condition: price >= 260 OR price <= 240
action: notify

# recurring_prompt  
schedule: "0 9 * * 1-5"  # 09:00 пн-пт
prompt: "Проверить портфель и новости по позициям"
action: analyze_and_report

# technical_signal
ticker: SBER
condition: RSI < 30 OR RSI > 70
action: notify
```

**Системный сервис мониторинга:**
```bash
# Запуск демона
./bin/monitor daemon

# Проверка условий каждую минуту
# Отправка уведомлений в Telegram/Email
```

---

### 3. Мультиагентная архитектура - MEDIUM

**Концепция из Stonki AI:**
Оркестратор делегирует задачи специализированным агентам:
- TA Agent → технический анализ
- News Agent → анализ новостей
- Scanner Agent → сканирование рынка
- User Memory Agent → память о пользователе

**Реализация через SKILL.md:**

Каждый skill уже является "агентом". Нужно добавить:
1. **Явную координацию** через AGENTS.md
2. **User Memory Skill** для хранения предпочтений

**Новый skill: memory**
```bash
memory get preference.risk_tolerance
memory set preference.sectors "нефтегаз, IT"
memory add trade_history --ticker=SBER --result=+12%
memory get user.profile
```

---

### 4. Code Execution - MEDIUM

**Концепция из Stonki AI:**
Python-код для сложных расчётов (особенно опционов).

**Реализация:**
```bash
calc execute --code="
import numpy as np
# Расчёт Kelly criterion
win_rate = 0.55
avg_win = 0.15
avg_loss = 0.10
kelly = (win_rate * avg_win - (1-win_rate) * avg_loss) / avg_win
print(f'Kelly: {kelly:.2%}')
"
```

Или предустановленные калькуляторы:
```bash
calc kelly --win-rate=0.55 --avg-win=0.15 --avg-loss=0.10
calc position-size --capital=1000000 --risk=0.02 --entry=250 --stop=240
calc sharpe --returns="[0.05, -0.02, 0.08]"
```

---

## Системные инструкции (System Prompts)

Иерархия из Stonki AI (5 уровней):

### Уровень 1: Meta-правила
```markdown
## professional_communication
- Тон: прямой, основанный на данных
- Без эмодзи в анализе
- Профессиональный, но не формальный

## data_freshness
- КРИТИЧНО: всегда использовать инструменты для текущих данных
- Никогда не предполагать цены

## mandatory_disclaimer
- КАЖДЫЙ ответ: "Не является инвестиционной рекомендацией"
```

### Уровень 2: Domain Logic
```markdown
## recipe_philosophy
- Рецепты = живые документы
- Эволюция через обновления

## monitoring_philosophy
- 3 типа: price_cross, schedule, technical_signal
- Когда использовать какой

## analysis_workflow
- Сбор данных → Анализ → Рекомендации
```

### Уровень 3: Safety & Compliance
```markdown
## limitations
- НЕ МОГУ: торговать, гарантировать прибыль
- МОГУ: анализировать, рекомендовать, отслеживать

## refusal_examples
- "Сколько мне купить?" → "Не могу рекомендовать размер позиции"
- "Что будет с ценой?" → "Не могу предсказывать будущее"
```

### Уровень 4: Output Layer
```markdown
## response_format
- Структурированные ответы
- Таблицы для метрик
- Ясные рекомендации с обоснованием
```

### Уровень 5: Context Input
```markdown
## user_preferences
- Профиль инвестора
- Толерантность к риску
- История сделок
```

---

## План реализации

### Phase 1: Recipes + Monitoring (2-3 недели)
1. Создать `recipe` skill
2. Создать `monitor` skill
3. Обновить AGENTS.md с новыми сценариями
4. Добавить системные инструкции

### Phase 2: Memory + Calculators (1-2 недели)
1. Создать `memory` skill
2. Создать `calc` skill
3. Интегрировать в workflow

### Phase 3: Social & Web (опционально)
1. Telegram уведомления для мониторинга
2. Web search через внешний API

---

## Примеры промптинга

### Создание рецепта из торговой идеи

**Пользователь:** "Думаю купить Сбер на 260, цель 300, стоп 245"

**Агент:**
```
1. ./vendor/bin/moex security:trade-data SBER → текущая цена
2. ./vendor/bin/t-invest analyze:fundamental --ticker=SBER → P/E, дивиденды
3. ./vendor/bin/news news:fetch --ticker SBER → свежие новости
4. Создать рецепт с рассчитанным R:R = 2.67:1
5. Настроить мониторинг на уровень входа
```

### Еженедельный отчёт

**Пользователь:** "Как там мой портфель?"

**Агент:**
```
1. ./vendor/bin/t-invest portfolio:report --period=week
2. Для каждой позиции > 5% веса:
   - ./vendor/bin/news news:fetch --ticker=$TICKER
   - ./vendor/bin/t-invest analyze:technical --ticker=$TICKER
3. Проверить активные рецепты
4. Сформировать сводку с рекомендациями
```

### Скрининг по стратегии

**Пользователь:** "Найди недооценённые с дивидендами от 10%"

**Агент:**
```
1. ./vendor/bin/t-invest screen:stocks --min-dividend=10 --max-pe=8
2. Для каждого кандидата:
   - ./vendor/bin/moex security:trade-data $TICKER
   - ./vendor/bin/t-invest analyze:quick --ticker=$TICKER
3. Отсортировать по скорингу
4. Показать топ-5 с обоснованием
```
