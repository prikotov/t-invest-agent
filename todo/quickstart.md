# Quick Start Checklist

Минимум для начала работы с агентом.

---

## Сейчас (30 мин)

- [ ] Проверить `.env.local` — есть токен T-Invest?
- [ ] Запустить `./vendor/bin/skill portfolio:positions`
- [ ] Запустить `./vendor/bin/moex security:trade-data SBER`
- [ ] Запустить `./vendor/bin/news news:fetch --ticker SBER`

## Сегодня (2 часа)

- [ ] Создать `data/` директории
- [ ] Создать `prompts/portfolio-analysis.md`
- [ ] Создать `prompts/ticker-analysis.md`
- [ ] Обновить `AGENTS.md` с правилами
- [ ] Протестировать анализ портфеля

## На этой неделе

- [ ] Реализовать `recipe` skill
- [ ] Реализовать `monitor` skill
- [ ] Настроить cron для проверки алертов

---

## Команды для проверки

```bash
cd t-invest-agent

# Проверка токена
cat .env.local | grep TINKOFF

# Тест skills
./vendor/bin/skill portfolio:positions
./vendor/bin/moex security:trade-data SBER
./vendor/bin/news news:fetch --ticker SBER
./vendor/bin/skill analyze:technical --ticker=SBER

# Создание директорий
mkdir -p data/recipes data/monitors data/memory prompts

# Готово!
```

---

## Первый запуск агента

```bash
# С промптом
./bin/agent analyze:portfolio --prompt=@prompts/portfolio-analysis.md

# Или интерактивно
./bin/agent chat
> Проанализируй мой портфель
```
