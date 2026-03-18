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

- [x] Создать директории ✓

### 3. Промпт: Анализ портфеля

**Файл:** `prompts/portfolio-analysis.md`

- [x] Создать `prompts/portfolio-analysis.md` ✓

### 4. Промпт: Анализ тикера

**Файл:** `prompts/ticker-analysis.md`

- [x] Создать `prompts/ticker-analysis.md` ✓

### 5. Обновить AGENTS.md

Добавить секции:

- [x] Тикер-трекинг ✓
- [x] Data freshness ✓
- [x] Формат ответов ✓
- [x] Защитные механизмы (дисклеймер) ✓

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
