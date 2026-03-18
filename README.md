# T-Invest Agent

AI-агент для анализа инвестиционного портфеля и подготовки торговых идей.

## Установка

```bash
composer install
```

## Структура

```
t-invest-agent/
├── AGENTS.md              # Инструкции для AI-агента (opencode)
├── skills/                # Навыки агента
│   ├── moex/SKILL.md      # MOEX API
│   ├── tinvest/SKILL.md   # T-Invest API
│   └── news/SKILL.md      # News aggregator
├── vendor/bin/            # CLI бинарники
│   ├── moex               # MOEX CLI
│   ├── skill              # T-Invest CLI
│   └── news               # News CLI
└── bin/agent              # CLI агент
```

## Использование

### Агент CLI

```bash
./bin/agent              # Список бинарников (по умолчанию)
./bin/agent bin:list     # Список бинарников
./bin/agent skills:list  # Список навыков
```

### Навыки

Все команды описаны в `skills/*/SKILL.md`:

- `skills/tinvest/SKILL.md` — портфель, цены, операции, фундаменталка
- `skills/moex/SKILL.md` — рыночные данные MOEX
- `skills/news/SKILL.md` — агрегация новостей

## Для AI-агентов

Инструкции для AI-агентов находятся в `AGENTS.md`. Этот файл описывает:
- Доступные навыки
- Типовые сценарии работы
- Рабочий процесс анализа

## Зависимости

- `prikotov/moex-core` → https://github.com/prikotov/moex-core
- `prikotov/t-invest-core` → https://github.com/prikotov/t-invest-core
- `prikotov/news-core` → https://github.com/prikotov/news-core

## Конфигурация

Создайте `.env.local`:

```env
TINVEST_TOKEN=t.ваш_токен
TINVEST_ACCOUNT_ID=ваш_account_id
TINVEST_BASE_URL=https://invest-public-api.tbank.ru/rest/
LOG_LEVEL=info
```
