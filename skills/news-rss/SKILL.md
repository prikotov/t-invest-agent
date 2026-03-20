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

| Источник | Тип |
|----------|-----|
| Interfax | Новости |
| TASS | Новости |
| RIA Novosti | Новости |
| PRIME | Экономика |
| RBC | Финансы |
| Kommersant | Бизнес |

## Как использовать

**Шаг 1:** Получение свежих новостей

```bash
news news:fetch [опции]
```

Опции:

| Опция | Описание | Значения | По умолчанию |
|-------|----------|----------|--------------|
| --ticker | Фильтр по тикеру | SBER, GAZP, ... | все |
| --source | Источник | interfax, tass, rbc, ... | все |
| --search | Поиск по тексту | строка | — |
| --category | Категория | Экономика, Финансы, ... | все |
| --limit | Лимит записей | число | 20 |
| --format | Формат вывода | table, json | table |

Примеры:

```bash
news news:fetch --ticker SBER
news news:fetch --source=interfax --source=tass
news news:fetch --search "нефть" --search "ОПЕК+"
news news:fetch --category "Экономика" --limit=10
news news:fetch --ticker SBER --format=json
```

**Шаг 2:** Кэширование новостей

```bash
news news:cache [опции]
```

Опции:

| Опция | Описание | Значения | По умолчанию |
|-------|----------|----------|--------------|
| --source | Источник | interfax, tass, ... | все |
| --clear | Очистить старше N дней | число | — |

Примеры:

```bash
news news:cache
news news:cache --source=interfax
news news:cache --clear=30
```

**Шаг 3:** Поиск по кэшу

```bash
news news:search <query> [опции]
```

Параметры:

| Параметр | Описание |
|----------|----------|
| query | Поисковый запрос |

Опции:

| Опция | Описание | Значения | По умолчанию |
|-------|----------|----------|--------------|
| --source | Источник | interfax, tass, ... | все |
| --category | Категория | строка | все |
| --days | За последние N дней | число | 7 |

Примеры:

```bash
news news:search "Сбербанк"
news news:search "нефть" --category "Экономика"
news news:search "" --source=interfax --days=3
```

**Шаг 4:** Информация о кэше

```bash
news news:cache-stats
news news:sources
```

## Результат

### news:fetch / news:search

Поля:

| Поле | Описание |
|------|----------|
| title | Заголовок |
| source | Источник |
| category | Категория |
| published | Дата публикации |
| link | Ссылка на статью |
| tickers | Найденные тикеры |

### news:cache-stats

Поля:

| Поле | Описание |
|------|----------|
| total | Всего записей |
| by_source | По источникам |
| oldest | Самая старая запись |
| newest | Самая новая запись |

## Типовые сценарии

### Новости по тикеру

```bash
news news:fetch --ticker SBER
```

Автоматически расширяет: SBER → "Сбербанк", "Sber", "SBER"

### Новости по теме

```bash
news news:fetch --search "нефть" --search "ОПЕК+"
```

### Поиск в архиве

```bash
news news:search "Сбербанк" --days=7 --category "Экономика"
```

## Интеграция

```bash
./vendor/bin/news news:fetch --ticker SBER
```

## Поддерживаемые тикеры

SBER, GAZP, LKOH, NVTK, ROSN, GMKN, YNDX, VTBR, TCSG, MOEX и др.
