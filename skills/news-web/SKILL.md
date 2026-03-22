---
name: news-web
description: Поиск новостей по новостным сайтам для анализа новостного фона компании
---

# News Web Search

Поиск новостей по новостным сайтам для анализа новостного фона компании.

**Источники:**
- **Interfax** — основной, работает через webfetch
- **Kommersant** — деловые новости, работает через webfetch  
- **RIA** — API endpoint, работает через webfetch
- **PRIME** — экономические новости, API endpoint
- **TASS** — требует headless Chrome (JS-челлендж)
- **RBC** — недоступен (блокирует curl и headless Chrome, сильная защита от ботов)

## Прямой доступ (webfetch)

| Источник | URL | Особенности |
|----------|-----|-------------|
| Interfax | `https://www.interfax.ru/search/?df={from}&dt={to}&phrase={query}` | Глубокий архив, поиск по всем разделам |
| Kommersant | `https://www.kommersant.ru/Search/Results?search_query={query}` | Деловые новости |
| RIA | `https://ria.ru/services/search/getmore/?query={query}&tags_limit=20&interval%5B%5D=year&sort%5B%5D=date` | API возвращает JSON-подобный формат |
| PRIME | `https://1prime.ru/services/search/getmore/?tags_limit=20&date_from={from}&date_to={to}&query={query}` | API с датами в формате YYYY-MM-DD |

## Только через браузер (требуют JS-челлендж)

| Источник | URL | Особенности |
|----------|-----|-------------|
| TASS | `https://tass.ru/search?search={query}&from_date={from}&to_date={to}` | Даты в ISO формате с Z: `2026-02-28T17:00:00.000Z` |

**TASS через headless Chrome:**

```bash
google-chrome --headless=new --disable-gpu --disable-blink-features=AutomationControlled \
  --window-size=1920,1080 \
  --user-agent="Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36" \
  --dump-dom --timeout=30000 \
  'https://tass.ru/search?search={QUERY}&from_date={FROM}&to_date={TO}' > tass.html
```

Параметры:
| Параметр | Описание |
|----------|----------|
| search | Поисковая фраза (URL-encoded) |
| from_date | Дата начала (ISO: `2026-02-28T17:00:00.000Z`) |
| to_date | Дата окончания (ISO: `2026-03-20T16:59:59.000Z`) |

Извлечение заголовков из HTML:
```bash
grep -oP 'MaterialCardLayout_text__LnYk4">\K[^<]+' tass.html
grep -oP 'NonMediaMaterialCardLayout_text__nc9M6">\K[^<]+' tass.html
```

## Как использовать

### Шаг 1: Определить поисковые термины

По тикеру определить название компании и варианты:

| Тикер | Название | Поисковые термины |
|-------|----------|-------------------|
| SBER | Сбербанк | "Сбербанк", "Sber", "Греф" |
| GAZP | Газпром | "Газпром", "Gazprom", "Миллер" |
| LKOH | Лукойл | "Лукойл", "Lukoil", "Алекперов" |
| YNDX | Яндекс | "Яндекс", "Yandex" |
| TCSG | Т-Банк | "Тинькофф", "Т-Банк", "Tinkoff" |

### Шаг 2: Поиск через Interfax (основной источник)

**Шаг 2.1:** Сформировать URL

Формат URL с датами:
```
https://www.interfax.ru/search/?df={FROM}&dt={TO}&phrase={QUERY}
```

Параметры:
| Параметр | Описание | Пример |
|----------|----------|--------|
| df | Дата начала | `01.03.2021` |
| dt | Дата окончания | `21.03.2026` |
| phrase | Поисковая фраза (URL-encoded) | `%D0%A1%D0%B5%D0%B3%D0%B5%D0%B6%D0%B0` |

**Шаг 2.2:** Выполнить запрос

Примеры:
```
https://www.interfax.ru/search/?df=01.03.2021&dt=21.03.2026&phrase=%D0%A1%D0%B5%D0%B3%D0%B5%D0%B6%D0%B0
https://www.interfax.ru/search/?df=01.01.2025&dt=21.03.2026&phrase=%D0%A1%D0%B1%D0%B5%D1%80%D0%B1%D0%B0%D0%BD%D0%BA
```

Примечание: Русский текст нужно URL-encode (Сегежа → %D0%A1%D0%B5%D0%B3%D0%B5%D0%B6%D0%B0)

**Шаг 2.3:** Извлечь результаты

Из ответа извлечь:
- Заголовки новостей (текст после категории и времени)
- Категорию (Экономика, В России, В мире)
- Дату публикации
- Ссылки на статьи

### Шаг 3: Поиск через Kommersant

```
https://www.kommersant.ru/Search/Results?search_query={QUERY}
```

Подходит для деловой аналитики, интервью с руководством.

### Шаг 4: Поиск через RIA

```
https://ria.ru/services/search/getmore/?query={QUERY}&tags_limit=20&interval%5B%5D=year&sort%5B%5D=date
```

Параметры:
| Параметр | Описание |
|----------|----------|
| query | Поисковая фраза (URL-encoded) |
| tags_limit | Лимит результатов |
| interval[] | Период: `year`, `month`, `week`, `all` |
| sort[] | Сортировка: `date`, `relevance` |

Примеры:
```
https://ria.ru/services/search/getmore/?query=%D0%A1%D0%B5%D0%B3%D0%B5%D0%B6%D0%B0&tags_limit=20&interval%5B%5D=year&sort%5B%5D=date
https://ria.ru/services/search/getmore/?query=%D0%A1%D0%B1%D0%B5%D1%80%D0%B1%D0%B0%D0%BD%D0%BA&tags_limit=50&interval%5B%5D=all&sort%5B%5D=date
```

### Шаг 4.1: Поиск через PRIME

```
https://1prime.ru/services/search/getmore/?tags_limit=20&date_from={FROM}&date_to={TO}&query={QUERY}
```

Параметры:
| Параметр | Описание |
|----------|----------|
| query | Поисковая фраза (URL-encoded) |
| tags_limit | Лимит результатов |
| date_from | Дата начала (YYYY-MM-DD) |
| date_to | Дата окончания (YYYY-MM-DD) |

Примеры:
```
https://1prime.ru/services/search/getmore/?tags_limit=20&date_from=2026-03-01&date_to=2026-03-21&query=%D0%A1%D0%B5%D0%B3%D0%B5%D0%B6%D0%B0
https://1prime.ru/services/search/getmore/?tags_limit=50&date_from=2025-01-01&date_to=2026-03-22&query=%D0%A1%D0%B1%D0%B5%D1%80%D0%B1%D0%B0%D0%BD%D0%BA
```

### Шаг 5: Чтение полных статей

Для важных статей открыть полный текст по ссылке и извлечь:
- Дату публикации
- Суть новости
- Заявления руководства
- Финансовые прогнозы
- Связанные события

### Шаг 8: Структурировать результаты

Сгруппировать новости по темам:

| Категория | Что искать |
|-----------|------------|
| Стратегия | Планы развития, новые направления |
| Финансы | Отчётность, дивиденды, долговая нагрузка |
| Риск-факторы | Суды, санкции, конфликты, проверки |
| Руководство | Назначения, заявления, интервью |
| Сделки | M&A, продажи активов, партнёрства |

## Типовые сценарии

### Анализ новой компании

```
1. Interfax: df=01.01.2024&dt=сегодня&phrase=Название
2. Извлечь: стратегия, планы, риски
3. Сравнить обещания год назад vs текущее состояние
```

### Проверка заявлений CEO

```
1. Interfax: phrase=Фамилия%20CEO&df=полгода&dt=сегодня
2. Kommersant: "Фамилия CEO" (интервью)
3. Сверить с реальными результатами
```

### Поиск риск-факторов

```
1. Interfax: phrase=компания%20(суд|проверка|скандал)
2. Kommersant: "компания" + (суд|проверка|скандал)
3. Оценить серьёзность и текущий статус
```

### Дивидендная история

```
1. Interfax: phrase=компания%20дивиденды&df=5%20лет&dt=сегодня
2. Извлечь: заявленные vs выплаченные
```

## Результат

Структурированная аналитика:

```
## [COMPANY] — Исторический обзор

### Стратегия и планы
- [Дата] План X → Статус: выполнен/нет
- [Дата] Заявление Y → Результат

### Риск-факторы
- [Дата] Событие → Исход

### Тренд заявлений руководства
- Оптимизм/Реализм → Тенденция

### Вывод
[Сводка по истории компании]
```

## Ограничения

- **Работает через webfetch:** Interfax, Kommersant, RIA, PRIME
- **TASS:** требует headless Chrome с флагом `--disable-blink-features=AutomationControlled`
- **RBC:** недоступен (блокирует все автоматические запросы)
- **Interfax** — лучший архив (10+ лет, точные даты)
