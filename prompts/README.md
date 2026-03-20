# Prompts

Промпты для анализа портфеля и генерации отчётов.

## Структура

```
prompts/
├── monitoring/
│   ├── morning-check.md      # Ежеутренняя проверка
│   └── weekly-report.md      # Недельный отчёт
└── portfolio-analysis-dashboard.md  # Полный анализ портфеля с дашбордом
```

## Использование

### Morning Check

```bash
agentic prompt monitoring/morning-check.md
```

Краткая проверка портфеля:
- Текущие цены
- Ночные новости
- Активные рецепты

### Portfolio Analysis Dashboard

```bash
agentic prompt portfolio-analysis-dashboard.md
```

Генерирует комплексный отчёт:
- **Фаза 0:** Разведка — проверка API, оценка масштаба
- **Фаза 1:** Сбор данных — портфель, позиции, свечи, новости
- **Фаза 2:** Анализ — структура, риски, доходность, рекомендации
- **Фаза 3:** Визуализации — интерактивный HTML-дашборд
- **Фаза 4:** Доставка — отчёт готов

**Результат:** `data/reports/{name}/{subname}-YYYYMMDD/report.html`

## Выходные данные

```
data/reports/
├── portfolio-analysis/
│   └── full-20260320/
│       ├── raw/              # Сырые данные API
│       │   ├── portfolio.json
│       │   ├── positions/
│       │   ├── candles/
│       │   └── news/
│       ├── analysis/         # Аналитика
│       │   ├── structure.json
│       │   ├── risk.json
│       │   ├── performance.json
│       │   └── recommendations.json
│       ├── charts/           # Данные графиков
│       └── report.html       # Интерактивный дашборд
└── morning-checks/
    └── daily-20260320/
        └── summary.md
```

## Skills

Промпты используют skills:
- `t-invest-portfolio-analysis` — Структура и метрики портфеля
- `t-invest-candles` — Исторические свечи OHLCV
- `t-invest-orderbook` — Стакан заявок
- `t-invest-corporate-events` — Корпоративные события
- `t-invest-trading-calendar` — Торговый календарь
- `moex-ticker-analysis` — Фундаментальный анализ
- `equity-research` — Профессиональная аналитика
- `social-sentiment` — Сентимент инвесторов
