# TODO: T-Invest Agent

Задачи для развития AI-агента.

---

## Статус

| Фаза | Название | Статус | Приоритет |
|------|----------|--------|-----------|
| 1 | MVP: Анализ портфеля | ✅ DONE | CRITICAL |
| 2 | Рецепты и мониторинг | ✅ DONE | HIGH |
| 3 | Память и персонализация | 🔄 50% | MEDIUM |

---

## Фаза 1: MVP — Анализ портфеля ✅

**Завершена 2026-03-18**

- [x] `skill portfolio:show` — работает
- [x] `moex security:trade-data` — работает
- [x] `news news:fetch` — работает
- [x] `skills/portfolio-analysis/SKILL.md` — готов
- [x] `skills/ticker-analysis/SKILL.md` — готов
- [x] `AGENTS.md` — правила обновлены

---

## Фаза 2: Рецепты и мониторинг ✅

**Завершена 2026-03-18**

### 2.1 Recipe Skill ✅

- [x] `skills/recipe/SKILL.md`
- [x] `scripts/recipe.php` — CLI
- [x] Хранение: `data/recipes/*.json`

### 2.2 Monitor Skill ✅

- [x] `skills/monitor/SKILL.md`
- [x] `scripts/monitor.php` — CLI
- [x] Хранение: `data/monitors/*.json`
- [x] `scripts/crontab.example` — cron

### 2.3 Промпты ✅

- [x] `prompts/morning-check.md`
- [x] `prompts/weekly-report.md`

---

## Фаза 3: Память и персонализация 🔄

### 3.1 Memory Skill ✅

- [x] `skills/memory/SKILL.md`
- [x] Хранение: `data/memory/`

### 3.2 Профиль пользователя

- [ ] `data/memory/user.json` с предпочтениями
- [ ] Авто-обновление позиций из портфеля

### 3.3 Учёт памяти

- [ ] Загрузка памяти в SKILL.md
- [ ] Учёт предпочтений при рекомендациях

---

## Структура

```
t-invest-agent/
├── AGENTS.md
├── data/
│   ├── recipes/
│   ├── monitors/
│   └── memory/
├── scripts/
│   ├── recipe.php
│   ├── monitor.php
│   └── crontab.example
├── prompts/
│   ├── monitoring/
│   │   ├── morning-check.md
│   │   └── weekly-report.md
│   ├── analysis/
│   └── actions/
├── skills/
│   ├── tinvest/
│   ├── moex/
│   ├── news/
│   ├── portfolio-analysis/
│   ├── ticker-analysis/
│   ├── recipe/
│   ├── monitor/
│   └── memory/
└── todo/
    ├── README.md
    └── phase2-recipes.md
```

---

## Следующие шаги

1. Фаза 3: Профиль пользователя в memory
2. Фаза 4: Social & Sentiment (опционально)

Не является инвестиционной рекомендацией.
