# T-Invest Agent Documentation

Документация по развитию проекта на основе анализа Stonki AI.

## Документы

| Файл | Описание |
|------|----------|
| [**STONKI-ANALYSIS.md**](STONKI-ANALYSIS.md) | Полный анализ возможностей Stonki AI: рецепты, мониторинг, агенты, API |
| [**MARKET-DATA-MAPPING.md**](MARKET-DATA-MAPPING.md) | Соответствие инструментов Stonki ↔ T-Invest Agent (OHLCV, опционы, метрики) |
| [**MULTI-AGENT-ARCHITECTURE.md**](MULTI-AGENT-ARCHITECTURE.md) | Мультиагентная архитектура: TA, News, Scanner, Memory, Social (российские соцсети) |
| [**ROADMAP.md**](ROADMAP.md) | Сравнение с Stonki AI, приоритеты, план реализации |
| [**COGNITIVE-ARCHITECTURE.md**](COGNITIVE-ARCHITECTURE.md) | Архитектура мышления агента: interleaved thinking, метакогниция, защитные механизмы |
| [**SYSTEM-PROMPTS.md**](SYSTEM-PROMPTS.md) | 5-уровневая иерархия инструкций + тикер-трекинг |
| [**RECIPES-EXAMPLES.md**](RECIPES-EXAMPLES.md) | 11 примеров рецептов + блок-схемы мониторинга |
| [**NEW-SKILLS.md**](NEW-SKILLS.md) | Описание новых skills: recipe, monitor, memory, calc, calendar, social |
| [**IMPLEMENTATION-IDEAS.md**](IMPLEMENTATION-IDEAS.md) | Полный список идей для реализации |

---

## Ключевые идеи из Stonki AI

### Архитектура

```
┌─────────────────────────────────────────────────────────────────┐
│                    ОРКЕСТРАТОР (Claude)                          │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │  • Анализ намерения пользователя                             │ │
│  │  • Выбор инструментов и агентов                              │ │
│  │  • Создание/обновление рецептов                              │ │
│  │  • Настройка мониторинга                                     │ │
│  │  • Формирование ответа                                       │ │
│  └─────────────────────────────────────────────────────────────┘ │
│                              │                                    │
│         ┌────────────────────┼────────────────────┐              │
│         ▼                    ▼                    ▼              │
│  ┌─────────────┐      ┌─────────────┐      ┌─────────────┐      │
│  │ TA Agent    │      │ News Agent  │      │ Scanner     │      │
│  │ (техника)   │      │ (новости)   │      │ (скрининг)  │      │
│  └─────────────┘      └─────────────┘      └─────────────┘      │
│         │                    │                    │              │
│         └────────────────────┼────────────────────┘              │
│                              ▼                                    │
│  ┌─────────────────────────────────────────────────────────────┐ │
│  │                    ИНСТРУМЕНТЫ                               │ │
│  │  • get_most_recent_bar (OHLCV)                              │ │
│  │  • get_ticker_details (компания)                            │ │
│  │  • get_symbol_summaries (метрики)                           │ │
│  │  • create_recipe / update_recipe                            │ │
│  │  • create_monitoring_task                                   │ │
│  │  • code_execution (Python)                                  │ │
│  └─────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

### Recipes (Рецепты)

**Концепция:** Живые торговые журналы, которые эволюционируют с рынком.

```yaml
title: "long-sber-dividends"
content: |
  Thesis: Дивидендный доход 12%+
  Entry: 255-260 ₽
  Target: 300 ₽
  Stop: 240 ₽
  R:R: 2.7:1
meta_data:
  tickers: [SBER]
  timeframes: [1d]
  tags: [dividend, value]
```

### Monitoring (Мониторинг)

**Три типа:**

| Тип | Назначение | Пример |
|-----|------------|--------|
| price_cross | Алерт на уровень | SBER >= 260 |
| technical_signal | Тех. условие | RSI < 30 |
| recurring_prompt | Периодическая проверка | Каждый день 09:00 |

### System Instructions (5 уровней)

```
1. Meta-правила          → Идентичность, коммуникация
2. Domain Logic          → Рецепты, мониторинг, workflow
3. Safety & Compliance   → Ограничения, дисклеймеры
4. Output Layer          → Формат ответов, auto-actions
5. Context Input         → Профиль, активные рецепты
```

---

## Что уже реализовано

| Stonki AI | T-Invest Agent | Статус |
|-----------|----------------|--------|
| `get_most_recent_bar` | `moex security:trade-data` | ✅ Базовый |
| `get_ticker_details` | `moex security:specification` | ✅ |
| `get_symbol_summaries` | `skill analyze:quick` | ✅ |
| `transfer_to_ta_agent` | `skill analyze:technical` | ✅ |
| `transfer_to_news_agent` | `news news:fetch` | ✅ |
| `transfer_to_scanner_agent` | `skill screen:stocks` | ✅ |
| — | `skill portfolio:analyze` | ✅ |
| — | `skill analyze:fundamental` | ✅ |

## Что требует доработки

| Stonki Agent | T-Invest Agent | Источники (для MOEX) | Приоритет |
|--------------|----------------|----------------------|-----------|
| `create_recipe` | `recipe create` | — | HIGH |
| `create_monitoring_task` | `monitor create` | — | HIGH |
| `transfer_to_user_memory_agent` | `memory` | Локальное хранение | HIGH |
| `get_week_earnings_events` | `calendar earnings` | MOEX ISS | MEDIUM |
| `get_most_recent_bar` (history) | `moex trade-data --history` | MOEX ISS | MEDIUM |
| `transfer_to_social_media_agent` | `social sentiment` | Telegram, Smart-Lab, Banki.ru | MEDIUM |
| `code_execution` | `calc` | — | MEDIUM |
| `get_option_chain` | — | MOEX FORTS | LOW |
| `transfer_to_websearch_agent` | — | Яндекс, Google | LOW |

---

## Источник

Анализ основан на статье:
https://prikotov.pro/blog/stonki-ai-personalnyi-khab-dlya-treidinga
