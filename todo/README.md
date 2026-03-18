# TODO: T-Invest Agent

Задачи для создания минимально работающего AI-агента.

---

## Статус

| Фаза | Название | Статус | Приоритет |
|------|----------|--------|-----------|
| 1 | MVP: Анализ портфеля | TODO | CRITICAL |
| 2 | Рецепты и мониторинг | TODO | HIGH |
| 3 | Память и персонализация | TODO | MEDIUM |

---

## Фаза 1: MVP — Анализ портфеля

Цель: Агент умеет анализировать портфель и давать рекомендации.

### 1.1 Подключение к T-Invest API

- [ ] Проверить работоспособность `skill portfolio:positions`
- [ ] Проверить работоспособность `skill portfolio:report`
- [ ] Протестировать `moex security:trade-data` для тикеров портфеля
- [ ] Протестировать `news news:fetch` для тикеров

**Файлы:**
- `t-invest-agent/.env.local` — токен T-Invest
- `t-invest-agent/skills/tinvest/SKILL.md`
- `t-invest-agent/skills/moex/SKILL.md`
- `t-invest-agent/skills/news/SKILL.md`

### 1.2 Базовый промпт для анализа портфеля

- [ ] Создать `prompts/portfolio-analysis.md`
- [ ] Промпт должен:
  - Получать позиции через `skill portfolio:positions`
  - Для каждой позиции > 5% веса:
    - `moex security:trade-data` — цена
    - `skill analyze:technical` — тех. картина
    - `news news:fetch` — новости
  - Формировать сводку с рекомендациями
  - Завершать дисклеймером

### 1.3 Еженедельный отчёт

- [ ] Создать `prompts/weekly-report.md`
- [ ] Промпт должен:
  - Вызывать `skill portfolio:report --period=week`
  - Анализировать изменения > 5%
  - Для изменившихся позиций — новости и тех. анализ
  - Формировать рекомендации на неделю

### 1.4 AGENTS.md update

- [ ] Добавить правила из `docs/SYSTEM-PROMPTS.md`:
  - Тикер-трекинг
  - Data freshness
  - Дисклеймер
  - Формат ответов

---

## Фаза 2: Рецепты и мониторинг

Цель: Агент умеет создавать рецепты и настраивать алерты.

### 2.1 Recipe skill

- [ ] Создать `skills/recipe/` структуру:
  ```
  skills/recipe/
  ├── SKILL.md
  ├── bin/recipe
  └── src/
      ├── create.php
      ├── list.php
      ├── show.php
      └── update.php
  ```
- [ ] CLI команды:
  - `recipe create --ticker=SBER --direction=LONG ...`
  - `recipe list`
  - `recipe show <id>`
  - `recipe update <id> --status=ACTIVE`
- [ ] Хранение: JSON в `data/recipes/`

### 2.2 Monitor skill

- [ ] Создать `skills/monitor/` структуру:
  ```
  skills/monitor/
  ├── SKILL.md
  ├── bin/monitor
  └── src/
      ├── create.php
      ├── list.php
      ├── check.php
      └── run.php
  ```
- [ ] CLI команды:
  - `monitor create price --ticker=SBER --level=260`
  - `monitor create schedule --cron="0 9 * * 1-5" --prompt=@morning`
  - `monitor list`
  - `monitor check` — проверить все алерты
  - `monitor run <id>` — выполнить scheduled prompt
- [ ] Хранение: JSON в `data/monitors/`
- [ ] Cron job для проверки алертов

### 2.3 Интеграция рецептов с мониторингом

- [ ] При создании рецепта автоматически создавать мониторинг
- [ ] При срабатывании мониторинга обновлять статус рецепта

### 2.4 Промпты для рецептов

- [ ] Создать `prompts/recipe-create.md`
- [ ] Промпт должен:
  - Запросить текущую цену
  - Провести анализ (фундаментал + техника)
  - Рассчитать R:R
  - Создать рецепт + мониторинг

---

## Фаза 3: Память и персонализация

Цель: Агент помнит предпочтения пользователя.

### 3.1 Memory skill

- [ ] Создать `skills/memory/` структуру:
  ```
  skills/memory/
  ├── SKILL.md
  ├── bin/memory
  └── src/
      ├── get.php
      ├── set.php
      ├── update.php
      └── clear.php
  ```
- [ ] CLI команды:
  - `memory get <key>`
  - `memory set <key> <value>`
  - `memory update <key> <value>`
- [ ] Хранение: JSON в `data/memory/user.json`

### 3.2 Профиль пользователя

- [ ] Создать `data/memory/user.json` с полями:
  ```json
  {
    "risk_tolerance": "moderate",
    "horizon": "long-term",
    "style": "value",
    "favorite_sectors": ["financial", "energy"],
    "positions": []
  }
  ```
- [ ] Обновлять позиции автоматически из `skill portfolio:positions`

### 3.3 Учет памяти в промптах

- [ ] Добавить загрузку памяти в промпты
- [ ] Учитывать предпочтения при рекомендациях

---

## Фаза 4: Social & Sentiment (опционально)

### 4.1 Social skill

- [ ] Интеграция с Telegram API
- [ ] Парсинг Smart-Lab
- [ ] Парсинг Banki.ru
- [ ] Анализ сентимента

---

## Структура директорий

```
t-invest-agent/
├── AGENTS.md
├── data/
│   ├── recipes/
│   ├── monitors/
│   └── memory/
│       └── user.json
├── prompts/
│   ├── portfolio-analysis.md
│   ├── weekly-report.md
│   └── recipe-create.md
├── skills/
│   ├── recipe/
│   ├── monitor/
│   └── memory/
└── todo/
    ├── README.md (этот файл)
    └── phase1-mvp.md
```

---

## Минимум для запуска (MVP)

Чтобы агент работал с портфелем, нужно:

1. ✅ `skill portfolio:positions` — работает
2. ✅ `moex security:trade-data` — работает
3. ✅ `news news:fetch` — работает
4. ⬜ Промпт `portfolio-analysis.md`
5. ⬜ Обновить `AGENTS.md` с правилами

**Оценка времени:** 2-3 часа

---

## Следующие шаги

1. Протестировать существующие skills
2. Создать `prompts/portfolio-analysis.md`
3. Обновить `AGENTS.md`
4. Протестировать анализ портфеля

Не является инвестиционной рекомендацией.
