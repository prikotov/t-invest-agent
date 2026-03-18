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
├── vendor/bin/            # CLI команды
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

### CLI бинарники

```bash
# MOEX - рыночные данные
./vendor/bin/moex security:specification SBER
./vendor/bin/moex security:trade-data SBER
./vendor/bin/moex security:indices SBER

# T-Invest - портфель
./vendor/bin/skill portfolio:positions
./vendor/bin/skill portfolio:analyze

# News - новости
./vendor/bin/news news:fetch --ticker SBER
./vendor/bin/news news:search "нефть"
```

## Для AI-агентов

Инструкции для AI-агентов находятся в `AGENTS.md`. Этот файл описывает:
- Доступные навыки и команды
- Типовые сценарии работы
- Рабочий процесс анализа

## Зависимости

- `prikotov/moex-core` → https://github.com/prikotov/moex-core
- `prikotov/t-invest-core` → https://github.com/prikotov/t-invest-core
- `prikotov/news-core` → https://github.com/prikotov/news-core

## Конфигурация

Создайте `.env.local`:

```env
TINKOFF_TOKEN=your_token
TINKOFF_ACCOUNT_ID=your_account_id
```
