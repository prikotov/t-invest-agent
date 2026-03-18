# T-Invest Agent Documentation Index

AI-агент для финансового анализа российского фондового рынка (MOEX).

---

## Quick Start

```
Анализ тикера → moex security:trade-data SBER
Портфель → skill portfolio:report --period=week
Новости → news news:fetch --ticker SBER
Рецепт → recipe create --ticker=SBER ...
Мониторинг → monitor create price --ticker=SBER --level=260
```

---

## Документы

### Развёртывание

| Документ | Описание | Строк |
|----------|----------|-------|
| [**DEPLOYMENT.md**](DEPLOYMENT.md) | Docker, install.sh, Quick Start для пользователей | — |

### Архитектура

| Документ | Описание | Строк |
|----------|----------|-------|
| [STONKI-ANALYSIS.md](STONKI-ANALYSIS.md) | Анализ Stonki AI: рецепты, мониторинг, агенты | 652 |
| [MULTI-AGENT-ARCHITECTURE.md](MULTI-AGENT-ARCHITECTURE.md) | Мультиагентная архитектура: TA, News, Scanner | 415 |
| [COGNITIVE-ARCHITECTURE.md](COGNITIVE-ARCHITECTURE.md) | Interleaved thinking, память, метакогниция | 471 |
| [SYSTEM-PROMPTS.md](SYSTEM-PROMPTS.md) | 5 уровней инструкций + тикер-трекинг | 500 |

### Реализация

| Документ | Описание | Строк |
|----------|----------|-------|
| [MARKET-DATA-MAPPING.md](MARKET-DATA-MAPPING.md) | Stonki ↔ T-Invest соответствие API | 205 |
| [NEW-SKILLS.md](NEW-SKILLS.md) | CLI: recipe, monitor, memory, social, calc | 691 |
| [RECIPES-EXAMPLES.md](RECIPES-EXAMPLES.md) | 11 рецептов + блок-схемы мониторинга | 810 |
| [IMPLEMENTATION-IDEAS.md](IMPLEMENTATION-IDEAS.md) | Полный список идей для промптинга | 489 |
| [ROADMAP.md](ROADMAP.md) | Приоритеты, план реализации | 311 |

---

## Иерархия системных инструкций

```
Уровень 0: Ticker Tracking     → <TICKERS> в каждом сообщении
Уровень 1: Meta-правила        → Идентичность, данные, дисклеймер
Уровень 2: Domain Logic        → Рецепты, мониторинг, анализ
Уровень 3: Safety & Compliance → Ограничения, отказы
Уровень 4: Output Layer        → Формат ответов, авто-действия
Уровень 5: Context Input       → Профиль, рецепты, мониторинг
```

---

## Иерархия приоритетов

```
1. Legal Compliance    → Дисклеймер всегда
2. Data Freshness      → Инструменты для актуальных цен
3. Professional Comm   → Тон, без эмодзи в анализе
4. Domain Logic        → Рецепты, мониторинг
5. Output Format       → Таблицы, структура
```

---

## Иерархия памяти

```
1. User Memory      → Персональные предпочтения, позиции
2. Global Memory    → Общие знания, шаблоны
3. General Guidelines → Системные правила
```

---

## Типы мониторинга

| Тип | Когда использовать |
|-----|-------------------|
| `price_cross` | "Сообщи когда SBER будет 260" |
| `technical_signal` | "Следи за RSI > 70" |
| `recurring_prompt` | "Каждое утро проверяй портфель" |

---

## Новые skills (приоритет)

| Skill | Приоритет | Описание |
|-------|-----------|----------|
| `recipe` | HIGH | Торговые идеи с уровнями |
| `monitor` | HIGH | Алерты и scheduled prompts |
| `memory` | HIGH | Память пользователя |
| `social` | MEDIUM | Telegram, Smart-Lab, Banki.ru |
| `calc` | MEDIUM | Python execution для расчётов |
| `calendar` | MEDIUM | Отчёты, дивиденды, события |

---

## Cognitive Pipeline

```
1. Intent Analysis    → Что хочет пользователь?
2. Data Validation    → Какие инструменты нужны?
3. Action Selection   → Создать рецепт? Мониторинг?
4. Response Formation → Структурированный ответ + дисклеймер
```

---

## Защитные механизмы

- **Hallucination Prevention** — всегда инструменты для цен
- **Compliance Enforcement** — дисклеймер, отказ от советов
- **Quality Gates** — логика, тон, полнота параметров

---

## Структура рецепта

```yaml
id: long-sber-breakout
ticker: SBER
direction: LONG
thesis: "Основная идея"
context: "Почему сейчас"
entry: { zone: [268, 272] }
target: { price: 300 }
stop: { price: 255 }
risk_reward: 2.2:1
monitoring: [price_cross на 270, 300, 255]
```

---

## Статистика

- **Файлов:** 10
- **Строк:** 4673
- **Рецептов:** 11 примеров
- **Skills:** 6 новых

---

Не является инвестиционной рекомендацией.
