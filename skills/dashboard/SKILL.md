---
name: dashboard
description: Создание интерактивных HTML-дашбордов с ECharts графиками для визуализации портфеля и котировок.
---

# Dashboard

HTML-шаблоны для интерактивных дашбордов.

## Когда использовать

- Создание отчёта по инвестиционному портфелю
- Визуализация котировок и технического анализа
- Отображение списка наблюдения (watchlist)
- Детальная страница акции с графиками и метриками

## Шаблоны

| Шаблон                 | Назначение                                                              |
|------------------------|-------------------------------------------------------------------------|
| `portfolio.html`       | Дашборд портфеля: сводка, график доходности, таблица активов, аллокация |
| `stock-details.html`   | Детальная страница акции: свечи, дивиденды, финансы, новости, профиль   |
| `watchlist.html`       | Список наблюдения: таблица тикеров с sparklines и метриками             |
| `stock-dashboard.html` | Общий дашборд рынка: сводка, топы, графики                              |

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

| Placeholder                     | Описание         | Пример             |
|---------------------------------|------------------|--------------------|
| `Apple Inc. (AAPL)`             | Название и тикер | `Сбербанк (SBER)`  |
| `$226.51`                       | Цена             | `₽255.50`          |
| `+0.62 (0.27%)`                 | Изменение        | `+5.20 (+2.1%)`    |
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

| Класс                                    | Описание      |
|------------------------------------------|---------------|
| `echart-basic-candlestick-chart-example` | Базовый       |
| `echart-candlestick-mixed-chart-example` | Свечи + объём |

### Линейные

| Класс                               | Описание   |
|-------------------------------------|------------|
| `echart-line-chart-example`         | Базовый    |
| `echart-area-line-chart-example`    | С заливкой |
| `echart-stacked-line-chart-example` | Стековый   |

### Bar

| Класс                                   | Описание           |
|-----------------------------------------|--------------------|
| `echart-basic-bar-chart-example`        | Базовый            |
| `echart-horizontal-bar-chart-example`   | Горизонтальный     |
| `echart-stacked-bar-chart-example`      | Стековый           |
| `echart-bar-race-chart-example`         | Анимированный race |
| `echart-bar-gradient-chart-example`     | Градиент           |

### Pie / Doughnut

| Класс                                   | Описание |
|-----------------------------------------|----------|
| `echart-pie-chart-example`              | Круговая |
| `echart-doughnut-rounded-chart-example` | Кольцо   |

### Gauge

| Класс                           | Описание    |
|---------------------------------|-------------|
| `echart-gauge-chart-example`    | Спидометр   |
| `echart-multi-ring-gauge-chart-example` | Мульти-кольцо |

### Radar

| Класс                                   | Описание    |
|-----------------------------------------|-------------|
| `echart-radar-chart-example`            | Базовый     |
| `echart-radar-customized-chart-example` | Кастомный   |
| `echart-radar-multiple-chart-example`   | Мульти      |

### Scatter

| Класс                                      | Описание      |
|--------------------------------------------|---------------|
| `echart-basic-scatter-chart-example`       | Базовый       |
| `echart-bubble-chart-example`              | Пузырьковый   |
| `echart-quartet-scatter-chart-example`     | Quartet       |
| `echart-single-axis-scatter-chart-example` | Single axis   |

### Heatmap

| Класс                                          | Описание        |
|------------------------------------------------|-----------------|
| `echart-heatmap-chart-example`                 | Базовый         |
| `echart-heatmap-single-series-chart-example`   | Single series   |

### Geo Map

| Класс                              | Описание   | Требует            |
|------------------------------------|------------|--------------------|
| `echart-session-by-country-map`    | Карта мира | `assets/data/world.js` |
| `echart-map-usa-example`           | Карта США  | `assets/data/usa.js`   |

### Sparkline (мини)

| Класс                                  | Цвет      | Применение    |
|----------------------------------------|-----------|---------------|
| `echart-stock-overview-chart`          | Зелёный   | Рост          |
| `echart-stock-overview-inverted-chart` | Красный   | Падение       |
| `echart-stock-overview-mixed-chart`    | Смешанный | Волатильность |

```html
<div class="echart-stock-overview-chart" 
     style="width:80px; min-height:44px" 
     data-echarts='{"data":[70,50,85,45,200,193]}'>
</div>
```

---

## Справочник CSS

### Цвета

| Класс                                    | Применение      |
|------------------------------------------|-----------------|
| `text-success` / `badge-phoenix-success` | Рост            |
| `text-danger` / `badge-phoenix-danger`   | Падение         |
| `text-body-tertiary`                     | Вторичный текст |
| `bg-body-emphasis`                       | Акцентный фон   |

### Размеры

| Класс   | Размер  |
|---------|---------|
| `fs-7`  | 1.5rem  |
| `fs-9`  | 0.85rem |
| `fs-10` | 0.75rem |

---

## Структура skill

```
skills/dashboard/
├── SKILL.md
├── README.md                  # Справочник по шаблонам
└── templates/
    ├── portfolio.html         # Дашборд портфеля
    ├── stock-details.html     # Детальная страница акции
    ├── watchlist.html         # Список наблюдения
    ├── stock-dashboard.html   # Общий дашборд рынка
    ├── echarts-examples/      # Примеры ECharts графиков
    │   ├── line-charts.html
    │   ├── bar-charts.html
    │   ├── candlestick-charts.html
    │   ├── pie-charts.html
    │   ├── gauge-chart.html
    │   ├── radar-charts.html
    │   ├── scatter-charts.html
    │   ├── heatmap-charts.html
    │   └── geo-map.html
    └── assets/
        ├── css/               # CSS стили
        ├── js/                # JS + ECharts
        ├── img/               # Изображения
        ├── data/              # GeoJSON данные (world.js, usa.js)
        └── vendors/           # Сторонние библиотеки
```

---

## ECharts Examples

Готовые примеры графиков в `templates/echarts-examples/`:

| Файл                  | Графики                                          |
|-----------------------|--------------------------------------------------|
| `line-charts.html`    | Line, Area, Stacked, Step, Gradient              |
| `bar-charts.html`     | Basic, Horizontal, Stacked, Race, Gradient, etc. |
| `candlestick-charts.html` | Basic, Mixed                                 |
| `pie-charts.html`     | Pie, Doughnut, Rounded, Multiple                 |
| `gauge-chart.html`    | Gauge/Спидометр                                  |
| `radar-charts.html`   | Radar, Customized, Multiple                      |
| `scatter-charts.html` | Basic, Bubble, Quartet, Single axis              |
| `heatmap-charts.html` | Heatmap, Heatmap single series                   |
| `geo-map.html`        | World map, USA map                               |

Открыть пример напрямую в браузере:
```bash
firefox skills/dashboard/templates/echarts-examples/line-charts.html
```
