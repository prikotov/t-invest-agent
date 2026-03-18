# Фаза 2: Рецепты и мониторинг

Торговые идеи и система алертов.

---

## Цель

После завершения:

1. Создавать рецепты для торговых идей
2. Настраивать ценовые алерты
3. Настраивать scheduled prompts
4. Автоматически связывать рецепты и мониторинг

---

## 2.1 Recipe Skill

### Структура

```
skills/recipe/
├── SKILL.md
├── bin/
│   └── recipe
├── src/
│   ├── Command/
│   │   ├── CreateCommand.php
│   │   ├── ListCommand.php
│   │   ├── ShowCommand.php
│   │   └── UpdateCommand.php
│   └── RecipeManager.php
└── composer.json
```

### CLI команды

```bash
# Создать рецепт
recipe create \
  --ticker=SBER \
  --direction=LONG \
  --entry="268-272" \
  --target=300 \
  --stop=255 \
  --thesis="Пробой сопротивления на объёме"

# Список рецептов
recipe list
recipe list --status=WATCHING
recipe list --ticker=SBER

# Показать рецепт
recipe show long-sber-breakout

# Обновить статус
recipe update long-sber-breakout --status=ACTIVE
recipe update long-sber-breakout --actual-entry=270

# Архивировать
recipe archive long-sber-breakout
```

### Формат хранения

**Файл:** `data/recipes/long-sber-breakout.json`

```json
{
  "id": "long-sber-breakout",
  "ticker": "SBER",
  "direction": "LONG",
  "status": "WATCHING",
  "thesis": "Пробой сопротивления на объёме",
  "context": "...",
  "entry": {
    "zone": [268, 272],
    "type": "limit"
  },
  "target": {
    "price": 300,
    "reason": "Исторический максимум"
  },
  "stop": {
    "price": 255,
    "reason": "Ниже поддержки"
  },
  "risk_reward": 2.2,
  "monitoring_ids": ["sber-270", "sber-300", "sber-255"],
  "created": "2024-01-15T10:00:00Z",
  "updated": "2024-01-15T10:00:00Z"
}
```

### Задачи

- [ ] Создать структуру директорий
- [ ] Реализовать `recipe create`
- [ ] Реализовать `recipe list`
- [ ] Реализовать `recipe show`
- [ ] Реализовать `recipe update`
- [ ] Написать SKILL.md
- [ ] Добавить в composer.json

---

## 2.2 Monitor Skill

### Структура

```
skills/monitor/
├── SKILL.md
├── bin/
│   └── monitor
├── src/
│   ├── Command/
│   │   ├── CreateCommand.php
│   │   ├── ListCommand.php
│   │   ├── CheckCommand.php
│   │   └── RunCommand.php
│   ├── MonitorManager.php
│   └── Checker/
│       ├── PriceChecker.php
│       └── ScheduleChecker.php
└── composer.json
```

### CLI команды

```bash
# Ценовой алерт
monitor create price \
  --ticker=SBER \
  --level=260 \
  --direction=above \
  --message="SBER достиг 260 ₽"

# Технический сигнал
monitor create technical \
  --ticker=SBER \
  --indicator=RSI \
  --condition="greater_than" \
  --value=70

# Scheduled prompt
monitor create schedule \
  --name="morning-check" \
  --cron="0 9 * * 1-5" \
  --prompt="@prompts/morning-check.md"

# Список
monitor list
monitor list --type=price
monitor list --ticker=SBER

# Проверить все (для cron)
monitor check

# Выполнить scheduled
monitor run morning-check

# Управление
monitor pause morning-check
monitor resume morning-check
monitor delete sber-260
```

### Формат хранения

**Price monitor:** `data/monitors/sber-260.json`

```json
{
  "id": "sber-260",
  "type": "price_cross",
  "ticker": "SBER",
  "level": 260,
  "direction": "above",
  "message": "SBER достиг 260 ₽",
  "status": "ACTIVE",
  "triggered_at": null,
  "recipe_id": "long-sber-breakout",
  "created": "2024-01-15T10:00:00Z"
}
```

**Scheduled prompt:** `data/monitors/morning-check.json`

```json
{
  "id": "morning-check",
  "type": "recurring_prompt",
  "name": "Ежеутренняя проверка",
  "cron": "0 9 * * 1-5",
  "prompt_file": "prompts/morning-check.md",
  "status": "ACTIVE",
  "last_run": "2024-01-15T09:00:00Z",
  "next_run": "2024-01-16T09:00:00Z",
  "created": "2024-01-10T10:00:00Z"
}
```

### Задачи

- [ ] Создать структуру директорий
- [ ] Реализовать `monitor create price`
- [ ] Реализовать `monitor create schedule`
- [ ] Реализовать `monitor list`
- [ ] Реализовать `monitor check` (проверка цен)
- [ ] Реализовать `monitor run` (выполнение prompt)
- [ ] Настроить cron для `monitor check`
- [ ] Написать SKILL.md
- [ ] Добавить в composer.json

---

## 2.3 Промпты для мониторинга

### Ежеутренняя проверка

**Файл:** `prompts/morning-check.md`

```markdown
# Morning Portfolio Check

Выполни ежеутреннюю проверку портфеля.

1. Получить позиции: skill portfolio:positions
2. Для каждой позиции:
   - Текущая цена: moex security:trade-data $TICKER
   - Новостей за ночь: news news:fetch --ticker $TICKER --days=1
3. Проверить активные рецепты:
   - Загрузить из data/recipes/*.json
   - Проверить: цена достигла уровней?
4. Сформировать краткий отчёт:
   - Изменения цен (%)
   - Важные новости
   - Срабатывания алертов

Формат: компактный.
Не является инвестиционной рекомендацией.
```

### Еженедельный отчёт

**Файл:** `prompts/weekly-report.md`

```markdown
# Weekly Portfolio Report

Выполни еженедельный анализ портфеля.

1. Получить отчёт: skill portfolio:report --period=week
2. Для позиций с изменением > 5%:
   - Причина: news news:fetch --ticker $TICKER --days=7
   - Техника: skill analyze:technical --ticker=$TICKER
3. Проверить активные рецепты:
   - Статус каждого
   - Нужно обновление?
4. Рекомендации на неделю:
   - Что держать
   - Что рассмотреть для продажи
   - Что рассмотреть для покупки

Не является инвестиционной рекомендацией.
```

### Задачи

- [ ] Создать `prompts/morning-check.md`
- [ ] Создать `prompts/weekly-report.md`

---

## 2.4 Интеграция

### Автоматическое создание мониторинга

При `recipe create`:
1. Создать price_cross на entry.lower
2. Создать price_cross на target
3. Создать price_cross на stop
4. Связать через `monitoring_ids`

### Обновление рецепта при триггере

При срабатывании price_cross:
1. Найти связанный рецепт
2. Обновить статус:
   - Entry → ACTIVE
   - Target → COMPLETED
   - Stop → STOPPED
3. Записать actual_entry/actual_exit

### Задачи

- [ ] Реализовать авто-создание мониторинга
- [ ] Реализовать обновление рецепта при триггере
- [ ] Добавить уведомления (Telegram?)

---

## Критерий готовности

- [ ] `recipe create` создаёт рецепт + мониторинг
- [ ] `recipe list` показывает все рецепты
- [ ] `monitor check` проверяет цены и триггерит
- [ ] `monitor run` выполняет scheduled prompt
- [ ] Cron настроен для автоматической проверки

---

## Оценка времени

| Задача | Время |
|--------|-------|
| Recipe skill | 4 часа |
| Monitor skill | 4 часа |
| Промпты | 1 час |
| Интеграция | 2 часа |
| Тестирование | 2 часа |
| **Итого** | **13 часов** |
