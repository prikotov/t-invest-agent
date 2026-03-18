# TODO: T-Invest Agent

Задачи для создания минимально работающего AI-агента.

---

## Статус

| Фаза | Название | Статус | Приоритет |
|------|----------|--------|-----------|
| 1 | MVP: Анализ портфеля | ✅ DONE | CRITICAL |
| 2 | Рецепты и мониторинг | TODO | HIGH |
| 3 | Память и персонализация | TODO | MEDIUM |

---

## Фаза 1: MVP — Анализ портфеля

Цель: Агент умеет анализировать портфель и давать рекомендации.

### 1.1 Подключение к T-Invest API

- [x] Проверить работоспособность `skill portfolio:show` ✓
- [x] Протестировать `moex security:trade-data` ✓
- [x] Протестировать `news news:fetch` ✓

**Файлы:**
- `t-invest-agent/.env.local` — токен T-Invest
- `t-invest-agent/skills/tinvest/SKILL.md`
- `t-invest-agent/skills/moex/SKILL.md`
- `t-invest-agent/skills/news/SKILL.md`

### 1.2 Базовый промпт для анализа портфеля

- [x] Создать `skills/portfolio-analysis/SKILL.md` ✓
- [x] Skill умеет:
  - Получать позиции через `skill portfolio:show` ✓
  - Получать цены через `moex security:trade-data` ✓
  - Получать новости через `news news:fetch` ✓
  - Получать фундаменталку через `skill instruments:fundamentals` ✓
  - Завершать дисклеймером ✓

### 1.3 Еженедельный отчёт

- [ ] Создать `prompts/weekly-report.md`
- [ ] Промпт должен:
  - Вызывать `skill portfolio:report --period=week`
  - Анализировать изменения > 5%
  - Для изменившихся позиций — новости и тех. анализ
  - Формировать рекомендации на неделю

### 1.4 AGENTS.md update

- [x] Добавить правила из `docs/SYSTEM-PROMPTS.md`: ✓
  - [x] Тикер-трекинг ✓
  - [x] Data freshness ✓
  - [x] Дисклеймер ✓
  - [x] Формат ответов ✓

---

## Фаза 2: Рецепты и мониторинг

Цель: Агент умеет создавать рецепты и настраивать алерты.

### 2.1 Recipe skill

- [x] Создать `skills/recipe/` структуру ✓
- [x] CLI команды ✓
- [x] Хранение: JSON в `data/recipes/` ✓

### 2.2 Monitor skill

- [x] Создать `skills/monitor/` структуру ✓
- [x] CLI команды ✓
- [x] Хранение: JSON в `data/monitors/` ✓
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

- [x] Создать `skills/memory/` структуру ✓
- [x] CLI команды ✓
- [x] Хранение: JSON в `data/memory/user.json` ✓

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

1. ✅ `skill portfolio:show` — работает
2. ✅ `moex security:trade-data` — работает
3. ✅ `news news:fetch` — работает
4. ✅ Skill `portfolio-analysis` — работает
5. ✅ Обновить `AGENTS.md` с правилами — готово

**✅ MVP завершён (2026-03-18)**

---

## Следующие шаги

**Фаза 1 завершена.** Следующие шаги — Фаза 2:

1. Cron job для проверки алертов
2. Интеграция рецептов с мониторингом
3. Создать `prompts/recipe-create.md`

Не является инвестиционной рекомендацией.
