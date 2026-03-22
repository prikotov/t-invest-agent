---
name: news-rss
description: Свежие финансовые новости из RSS-лент (Интерфакс, ТАСС, РБК, Коммерсант)
---

# News RSS

Агрегация свежих финансовых новостей из RSS-лент в реальном времени.

## Когда использовать

- Получение свежих новостей по тикеру
- Мониторинг новостей по теме или категории
- Быстрый обзор рыночных событий
- Поиск релевантных новостей в архиве

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

Поиск новостей по ключевым словам с фильтрами. Опционально обновляет кэш из RSS. Используется для получения контекста по тикеру или теме.

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
| --days     | -d         | За последние N дней   | число                 | 7            |
| --limit    | -l         | Лимит записей         | число                 | 50           |
| --format   | -f         | Формат вывода         | md, json, csv, text   | md           |
| --no-fetch |            | Только поиск в кэше   | флаг                  | выкл         |

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

### Новости по тикеру

```bash
mkdir -p data/news-rss/results/2026-03-22
./vendor/bin/news news:search "Сбербанк" --format=json > data/news-rss/results/2026-03-22/search-sber-2026-03-22_14-30-00.json
```

### Новости по нескольким темам

```bash
mkdir -p data/news-rss/results/2026-03-22
./vendor/bin/news news:search "нефть" "ОПЕК+" --format=json > data/news-rss/results/2026-03-22/search-oil-opek-2026-03-22_14-30-00.json
```

### Поиск в архиве без обновления

```bash
mkdir -p data/news-rss/results/2026-03-22
./vendor/bin/news news:search "Сбербанк" --no-fetch --days=7 --format=json > data/news-rss/results/2026-03-22/search-sber-archive-2026-03-22_14-30-00.json
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

## Кэширование

- Структура: `{project}/data/news-rss/cache/YYYY/MM/DD/Source/`
- При каждом вызове `news:search` (без `--no-fetch`) кэш обновляется из RSS

## Поддерживаемые тикеры

SBER, GAZP, LKOH, NVTK, ROSN, GMKN, YNDX, VTBR, TCSG, MOEX и др.
