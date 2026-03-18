# Фаза 1: MVP — Анализ портфеля

Минимальный работающий агент для анализа портфеля.

---

## Цель

После завершения этой фазы агент сможет:

1. Получить позиции портфеля
2. Показать текущие цены
3. Найти новости по позициям
4. Дать рекомендации BUY/HOLD/SELL

---

## Задачи

### 1. Проверка существующих skills

**Файл:** `test-skills.sh`

```bash
#!/bin/bash
# Тестирование skills

echo "=== Portfolio ==="
./vendor/bin/skill portfolio:positions

echo "=== MOEX Trade Data ==="
./vendor/bin/moex security:trade-data SBER

echo "=== News ==="
./vendor/bin/news news:fetch --ticker SBER

echo "=== Technical Analysis ==="
./vendor/bin/skill analyze:technical --ticker=SBER
```

- [ ] Создать `test-skills.sh`
- [ ] Запустить и проверить вывод
- [ ] Зафиксировать проблемы

### 2. Создать директории

```bash
mkdir -p t-invest-agent/data/recipes
mkdir -p t-invest-agent/data/monitors
mkdir -p t-invest-agent/data/memory
mkdir -p t-invest-agent/prompts
```

- [ ] Создать директории

### 3. Промпт: Анализ портфеля

**Файл:** `prompts/portfolio-analysis.md`

```markdown
# Portfolio Analysis Prompt

## System

Ты — T-Invest Agent, AI-аналитик для российского фондового рынка.

## Правила

1. ВСЕГДА используй инструменты для актуальных данных
2. НИКОГДА не предполагай цены или метрики
3. КАЖДЫЙ ответ завершай: "Не является инвестиционной рекомендацией."
4. В каждом вызове инструмента добавляй тикеры: <TICKERS>SBER</TICKERS>

## Workflow

1. Получить позиции: skill portfolio:positions
2. Для каждой позиции > 5% веса:
   - Цена: moex security:trade-data $TICKER
   - Новости: news news:fetch --ticker $TICKER --days=7
   - Техника: skill analyze:technical --ticker=$TICKER
3. Сформировать сводку:
   - Таблица позиций с изменениями
   - Важные новости
   - Техническая картина
   - Рекомендации (HOLD/BUY MORE/REDUCE)

## Формат ответа

**Портфель: Сводка**

| Тикер | Вес | Цена | Изм 7д | Рекомендация |
|-------|-----|------|--------|--------------|
| SBER  | 25% | 255  | +3%    | HOLD         |

**Важные новости:**
- SBER: [новость]

**Техническая картина:**
- SBER: боковик, RSI 45

**Рекомендации:**
- [список]

Не является инвестиционной рекомендацией.
SBER, GAZP, LKOH
```

- [ ] Создать `prompts/portfolio-analysis.md`

### 4. Промпт: Анализ тикера

**Файл:** `prompts/ticker-analysis.md`

```markdown
# Ticker Analysis Prompt

## System

Ты — T-Invest Agent, AI-аналитик для российского фондового рынка.

## Workflow для {TICKER}

1. Цена: moex security:trade-data {TICKER}
2. Спецификация: moex security:specification {TICKER}
3. Фундаментал: skill analyze:fundamental --ticker={TICKER}
4. Техника: skill analyze:technical --ticker={TICKER}
5. Новости: news news:fetch --ticker {TICKER} --days=7

## Формат ответа

**{TICKER}: [Trend]**

| Метрика | Значение | Оценка |
|---------|----------|--------|
| Цена | X ₽ | — |
| P/E | X | ✓/✗ |
| Div Yield | X% | ✓/✗ |

**Техническая картина:**
- Тренд: ...
- RSI: ...
- Поддержка: ...
- Сопротивление: ...

**Новости:**
- [список]

**Рекомендация:** BUY / HOLD / SELL

Не является инвестиционной рекомендацией.
{TICKER}
```

- [ ] Создать `prompts/ticker-analysis.md`

### 5. Обновить AGENTS.md

Добавить секции:

- [ ] Тикер-трекинг
- [ ] Data freshness
- [ ] Формат ответов
- [ ] Защитные механизмы

### 6. Тестовый запуск

- [ ] Запустить анализ портфеля
- [ ] Проверить формат вывода
- [ ] Зафиксировать проблемы

---

## Критерий готовности

Фаза 1 завершена когда:

- [ ] `skill portfolio:positions` возвращает позиции
- [ ] `moex security:trade-data` возвращает цены
- [ ] `news news:fetch` возвращает новости
- [ ] Промпт `portfolio-analysis.md` работает
- [ ] Агент выдаёт структурированный отчёт с дисклеймером

---

## Оценка времени

| Задача | Время |
|--------|-------|
| Проверка skills | 30 мин |
| Создание директорий | 5 мин |
| Промпт portfolio-analysis | 30 мин |
| Промпт ticker-analysis | 20 мин |
| Обновление AGENTS.md | 30 мин |
| Тестирование | 30 мин |
| **Итого** | **2.5 часа** |
