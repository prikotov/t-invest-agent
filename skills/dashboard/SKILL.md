---
name: dashboard
description: Создание интерактивных HTML-дашбордов на основе Phoenix Theme с ECharts графиками для визуализации портфеля и котировок.
---

# Dashboard

HTML-шаблоны для интерактивных дашбордов на основе Phoenix Theme.

## Когда использовать

- Создание отчёта по инвестиционному портфелю
- Визуализация котировок и технического анализа
- Отображение списка наблюдения (watchlist)
- Детальная страница акции с графиками и метриками

## Шаблоны

| Шаблон | Назначение |
|--------|------------|
| `portfolio.html` | Дашборд портфеля: сводка, график доходности, таблица активов, аллокация |
| `stock-details.html` | Детальная страница акции: свечи, дивиденды, финансы, новости, профиль |
| `watchlist.html` | Список наблюдения: таблица тикеров с sparklines и метриками |
| `stock-dashboard.html` | Общий дашборд рынка: сводка, топы, графики |

## Как использовать

**Шаг 1:** Выбрать шаблон

Определить тип отчёта:
- Портфель инвестора → `portfolio.html`
- Анализ одной акции → `stock-details.html`
- Список отслеживаемых бумаг → `watchlist.html`
- Общая аналитика рынка → `stock-dashboard.html`

**Шаг 2:** Скопировать шаблон

```bash
cp skills/dashboard/templates/<шаблон>.html <путь>/report.html
```

Примеры:

```bash
# Отчёт по портфелю
cp skills/dashboard/templates/portfolio.html reports/my-portfolio.html

# Анализ акции
cp skills/dashboard/templates/stock-details.html reports/sber-analysis.html

# Watchlist
cp skills/dashboard/templates/watchlist.html reports/watchlist.html
```

**Шаг 3:** Заменить данные

Найти и заменить placeholder-данные в HTML:

| Placeholder | Описание | Пример |
|-------------|----------|--------|
| `Apple Inc. (AAPL)` | Название и тикер | `Сбербанк (SBER)` |
| `$226.51` | Цена | `₽255.50` |
| `+0.62 (0.27%)` | Изменение | `+5.20 (+2.1%)` |
| `data-echarts='{"data":[...]}'` | Данные sparkline | Реальные котировки |

Для ECharts графиков заменить данные в JavaScript или использовать data-атрибуты:

```html
<div class="echart-stock-overview-chart" 
     style="width:80px; min-height:44px" 
     data-echarts='{"data":[70,50,85,45,200,193,196,210]}'>
</div>
```

**Шаг 4:** Открыть в браузере

```bash
# Прямое открытие
firefox reports/my-portfolio.html

# Или через локальный сервер (для избежания CORS)
python -m http.server 8000 -d reports
# Затем открыть http://localhost:8000/my-portfolio.html
```

## Результат

**Файл:** `<путь>/report.html`

**Содержит:**
- HTML-разметка дашборда
- ECharts контейнеры для графиков (автоинициализация)
- Карточки с метриками
- Таблицы с сортировкой и пагинацией
- Переключатель темы (light/dark)
- Top navbar

**Открывается:** напрямую в браузере из папки templates

---

## Справочник ECharts

Графики инициализируются автоматически по CSS-классу.

### Свечи (Candlestick)

| Класс | Описание |
|-------|----------|
| `echart-basic-candlestick-chart-example` | Базовый |
| `echart-candlestick-mixed-chart-example` | Свечи + объём |

### Линейные

| Класс | Описание |
|-------|----------|
| `echart-line-chart-example` | Базовый |
| `echart-area-line-chart-example` | С заливкой |
| `echart-stacked-line-chart-example` | Стековый |

### Pie / Doughnut

| Класс | Описание |
|-------|----------|
| `echart-pie-chart-example` | Круговая |
| `echart-doughnut-rounded-chart-example` | Кольцо |

### Sparkline (мини)

| Класс | Цвет | Применение |
|-------|------|------------|
| `echart-stock-overview-chart` | Зелёный | Рост |
| `echart-stock-overview-inverted-chart` | Красный | Падение |
| `echart-stock-overview-mixed-chart` | Смешанный | Волатильность |

```html
<div class="echart-stock-overview-chart" 
     style="width:80px; min-height:44px" 
     data-echarts='{"data":[70,50,85,45,200,193]}'>
</div>
```

---

## Справочник CSS

### Цвета

| Класс | Применение |
|-------|------------|
| `text-success` / `badge-phoenix-success` | Рост |
| `text-danger` / `badge-phoenix-danger` | Падение |
| `text-body-tertiary` | Вторичный текст |
| `bg-body-emphasis` | Акцентный фон |

### Размеры

| Класс | Размер |
|-------|--------|
| `fs-7` | 1.5rem |
| `fs-9` | 0.85rem |
| `fs-10` | 0.75rem |

---

## Структура skill

```
skills/dashboard/
├── SKILL.md
├── references/
│   └── README.md
└── templates/
    ├── portfolio.html        # Дашборд портфеля
    ├── stock-details.html    # Детальная страница акции
    ├── watchlist.html        # Список наблюдения
    ├── stock-dashboard.html  # Общий дашборд рынка
    └── assets/
        ├── css/              # Phoenix Theme CSS
        ├── js/               # Phoenix JS + ECharts
        ├── img/              # Изображения
        └── vendors/          # Сторонние библиотеки
```
