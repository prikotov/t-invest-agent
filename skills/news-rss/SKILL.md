---
name: news-rss
description: Свежие финансовые новости из RSS-лент (Интерфакс, ТАСС, РБК, Коммерсант)
---

# News RSS

Поиск свежих финансовых новостей из RSS-лент.

## Когда использовать

- Получение свежих новостей по тикеру
- Мониторинг свежих новостей по теме
- Быстрый обзор рыночных событий

## Источники

| Источник    | Тип       |
|-------------|-----------|
| Interfax    | Новости   |
| TASS        | Новости   |
| RIA Novosti | Новости   |
| PRIME       | Экономика |
| RBC         | Финансы   |
| Kommersant  | Бизнес    |

## Как использовать

**Паттерн сохранения результатов:**

```
data/{skill}/results/{YYYY-MM-DD}/{operation}-{YYYY-MM-DD}_{HH-II-SS}.{format}
```

### news:search

Поиск свежих новостей по ключевым словам.

```bash
mkdir -p data/news-rss/results/2026-03-22
./vendor/bin/news news:search "SBER" --format=json > data/news-rss/results/2026-03-22/search-sber-2026-03-22_14-30-00.json
```

```bash
./vendor/bin/news news:search [query] [options]
```

Аргументы:

| Аргумент | Описание                     |
|----------|------------------------------|
| query    | Поисковые термины (опционально) |

Опции:

| Опция      | Сокращение | Описание              | Значения              | По умолчанию |
|------------|------------|-----------------------|-----------------------|--------------|
| --source   | -s         | Фильтр по источнику   | interfax, tass, rbc, ... | все       |
| --category |            | Фильтр по категории   | Экономика, Финансы, ... | все       |
| --limit    | -l         | Лимит записей         | число                 | 50           |
| --format   | -f         | Формат вывода         | md, json, csv, text   | md           |

### news:sources

Список доступных RSS-источников с URL лент. Используется для справки и фильтрации по источникам.

```bash
mkdir -p data/news-rss/results/2026-03-22
./vendor/bin/news news:sources --format=json > data/news-rss/results/2026-03-22/sources-2026-03-22_14-30-00.json
```

```bash
./vendor/bin/news news:sources [options]
```

Опции:

| Опция    | Сокращение | Описание      | Значения            | По умолчанию |
|----------|------------|---------------|---------------------|--------------|
| --format | -f         | Формат вывода | md, json, csv, text | md           |

## Результат

### news:search

Поля:

| Поле     | Описание            |
|----------|---------------------|
| Date     | Дата публикации     |
| Source   | Источник            |
| Category | Категория           |
| Title    | Заголовок           |
| Link     | Ссылка на статью    |

### news:sources

Поля:

| Поле   | Описание        |
|--------|-----------------|
| Name   | Название источника |
| URL    | URL RSS-ленты   |

## Типовые сценарии

### Последние новости по тикеру

```bash
mkdir -p data/news-rss/results/2026-03-22
./vendor/bin/news news:search "Сбербанк" --format=json > data/news-rss/results/2026-03-22/search-sber-2026-03-22_14-30-00.json
```

### Последние новости по нескольким темам

```bash
mkdir -p data/news-rss/results/2026-03-22
./vendor/bin/news news:search "нефть" "ОПЕК+" --format=json > data/news-rss/results/2026-03-22/search-oil-opek-2026-03-22_14-30-00.json
```

### Фильтр по источникам

```bash
mkdir -p data/news-rss/results/2026-03-22
./vendor/bin/news news:search "ОПЕК" --source=interfax --source=tass --format=json > data/news-rss/results/2026-03-22/search-opek-sources-2026-03-22_14-30-00.json
```

### Фильтр по категории

```bash
mkdir -p data/news-rss/results/2026-03-22
./vendor/bin/news news:search "ОПЕК" --category "Экономика" --format=json > data/news-rss/results/2026-03-22/search-opek-category-2026-03-22_14-30-00.json
```

## Поддерживаемые тикеры

SBER, GAZP, LKOH, NVTK, ROSN, GMKN, YNDX, VTBR, TCSG, MOEX и др.
