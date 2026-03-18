# TODO: T-Invest Agent

Задачи для развития AI-агента.

---

## Статус

| Фаза | Название | Статус | Приоритет |
|------|----------|--------|-----------|
| 1 | MVP: Анализ портфеля | ✅ DONE | CRITICAL |
| 2 | Рецепты и мониторинг | 🔄 80% | HIGH |
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

## Фаза 2: Рецепты и мониторинг 🔄

### 2.1 Recipe Skill ✅

- [x] `skills/recipe/SKILL.md`
- [x] Хранение: `data/recipes/*.json`

### 2.2 Monitor Skill ✅

- [x] `skills/monitor/SKILL.md`
- [x] `scripts/monitor.php` — CLI
- [x] Хранение: `data/monitors/*.json`
- [x] `scripts/crontab.example` — cron

### 2.3 Интеграция рецептов с мониторингом

- [ ] Авто-создание мониторов при `recipe create`
- [ ] Обновление статуса рецепта при триггере монитора

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
│   ├── monitor.php
│   └── crontab.example
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

1. Интеграция recipe ↔ monitor
2. Профиль пользователя в memory
3. Фаза 4: Social & Sentiment (опционально)

Не является инвестиционной рекомендацией.
