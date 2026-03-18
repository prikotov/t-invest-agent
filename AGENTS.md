# T-Invest Agent - Финансовый Аналитик

AI-агент для финансового анализа, построенный на composer-пакетах навыков.

## Архитектура

```
t-invest-agent/
├── AGENTS.md              # Этот файл - инструкции для AI-агента
├── skills/                # Собранные навыки из зависимостей
│   ├── moex/SKILL.md
│   ├── tinvest/SKILL.md
│   └── news/SKILL.md
├── vendor/                # Composer-зависимости
│   └── bin/               # Бинарники навыков
│       ├── moex           # MOEX CLI
│       ├── skill          # T-Invest CLI
│       └── news           # News CLI
└── bin/agent              # Главный CLI агент
```

## Навыки

### MOEX (Московская Биржа)
Получение рыночных данных: спецификации, цены, объёмы, индексы.

```bash
./vendor/bin/moex security:specification SBER
./vendor/bin/moex security:trade-data SBER
./vendor/bin/moex security:indices SBER
./vendor/bin/moex security:aggregates SBER
```

### T-Invest (Портфель)
Анализ портфеля, технический/фундаментальный анализ, ребалансировка.

```bash
./vendor/bin/skill portfolio:analyze
./vendor/bin/skill portfolio:report --period=week
./vendor/bin/skill analyze:quick --ticker=SBER
./vendor/bin/skill analyze:fundamental --ticker=SBER
./vendor/bin/skill portfolio:rebalance:plan --target=balanced
```

### News (Новости)
Агрегация и поиск финансовых новостей.

```bash
./vendor/bin/news news:fetch --ticker SBER
./vendor/bin/news news:fetch --search "нефть"
./vendor/bin/news news:search "Сбербанк" --days=7
```

## Типовые сценарии работы агента

### 1. Еженедельный мониторинг портфеля

```bash
# Получить отчёт по портфелю
./vendor/bin/skill portfolio:report --period=week

# Получить новости по позициям
./vendor/bin/news news:fetch --ticker SBER --ticker LKOH --ticker GAZP

# Проверить рыночные данные
./vendor/bin/moex security:trade-data SBER
```

**Действия агента:**
1. Проанализировать отчёт портфеля
2. Найти отклонения > 5% от целевого распределения
3. Проверить новости на события
4. Сформировать рекомендации

### 2. Анализ кандидата для покупки

```bash
# Быстрый анализ
./vendor/bin/skill analyze:quick --ticker=GAZP

# Фундаментальный анализ
./vendor/bin/skill analyze:fundamental --ticker=GAZP

# Новости по компании
./vendor/bin/news news:fetch --ticker GAZP

# Рыночные данные
./vendor/bin/moex security:specification GAZP
./vendor/bin/moex security:aggregates GAZP
```

**Действия агента:**
1. Оценить технические сигналы (тренд, RSI)
2. Оценить фундаментальные метрики (P/E, P/B, ROE)
3. Проверить ликвидность на MOEX
4. Найти релевантные новости
5. Сформировать рекомендацию

### 3. Ребалансировка портфеля

```bash
# Анализ распределения
./vendor/bin/skill portfolio:analyze

# Рекомендации по ребалансировке
./vendor/bin/skill portfolio:rebalance:plan --target=balanced

# Детальный анализ позиций для продажи
./vendor/bin/skill analyze:technical --ticker=SBER
./vendor/bin/skill analyze:fundamental --ticker=SBER
```

**Действия агента:**
1. Определить отклонения от целевого распределения
2. Для каждой позиции с отклонением провести анализ
3. Оценить момент для сделки (технический анализ)
4. Сформировать список сделок

### 4. Скрининг акций

```bash
# Поиск недооценённых с дивидендами
./vendor/bin/skill screen:stocks --min-dividend=7 --max-pe=8

# По сектору
./vendor/bin/skill screen:stocks --sector=financial --min-dividend=7
```

## Рабочий процесс агента

```
┌─────────────────────────────────────────────────────────────────┐
│  1. СБОР ДАННЫХ                                                 │
│  ───────────────                                                │
│  portfolio:analyze → metricks, allocation                       │
│  news:fetch → events by tickers                                 │
│  moex security:* → market data                                  │
└─────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│  2. АНАЛИЗ                                                      │
│  ────────                                                       │
│  • Отклонения от целевого распределения?                        │
│  • Технические сигналы?                                         │
│  • Фундаментальная оценка?                                      │
│  • Важные новости?                                              │
└─────────────────────────────────────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│  3. РЕКОМЕНДАЦИИ                                                │
│  ────────────────                                               │
│  BUY / SELL / HOLD с обоснованием                               │
│  Исполнение - вручную в приложении брокера                      │
└─────────────────────────────────────────────────────────────────┘
```

## Установка и обновление

```bash
# Установка
composer install

# Обновление навыков
composer update

# Переустановка skills
composer skills:install
# или
./bin/agent skills:install
```

## Конфигурация

Файл `.env.local` (создать на основе `.env`):

```env
# T-Invest API
TINKOFF_TOKEN=your_token
TINKOFF_ACCOUNT_ID=your_account_id
```

## Форматы вывода

Все команды поддерживают:
- `--format=table` - таблица (по умолчанию)
- `--format=json` - JSON для парсинга
- `--format=csv` - CSV для Excel

## Принципы работы

1. **Советник, не трейдер** - агент рекомендует, решения принимает человек
2. **Данные из API** - все данные реальные, не предполагаемые
3. **Прозрачность** - каждое решение обосновано метриками
4. **Безопасность** - read-only токен T-Invest достаточен

## Навыки агента

Навыки загружаются из `skills/*/SKILL.md` при установке зависимостей.

Для добавления нового навыка:
1. Создать composer-пакет со структурой `skills/name/SKILL.md`
2. Добавить в зависимости tinvest-agent
3. Выполнить `composer install`
