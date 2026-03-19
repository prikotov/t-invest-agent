# T-Invest Skill

Навык для работы с портфелем через T-Invest API.

## Команды

### Анализ портфеля
```bash
skill portfolio:analyze
skill portfolio:positions
skill portfolio:report --period=week
```
Возвращает: стоимость, доходность, распределение по классам/секторам, отклонения от цели

### Ребалансировка
```bash
skill portfolio:rebalance:plan --target=balanced
```
Возвращает: рекомендации по покупке/продаже с обоснованием

### Технический анализ
```bash
skill analyze:technical --ticker=SBER
```
Возвращает: тренд, RSI, MACD, Bollinger, сигналы

### Фундаментальный анализ
```bash
skill analyze:fundamental --ticker=SBER
```
Возвращает: P/E, P/B, ROE, дивиденды, справедливая цена, скоринг

### Быстрый анализ
```bash
skill analyze:quick --ticker=SBER
```
Возвращает: сводка технического + фундаментального

### Скрининг акций
```bash
skill screen:stocks --min-dividend=7 --max-pe=8 --sector=financial
```
Возвращает: отфильтрованный список с сигналом и скорингом

## Типовые сценарии

### Еженедельный мониторинг
```bash
skill portfolio:report --period=week
```
Проверить: сигналы, отклонения > 5%

### Ежемесячная ребалансировка
```bash
skill portfolio:analyze
skill portfolio:rebalance:plan --target=balanced
```
Принять решение и исполнить вручную.

### Анализ кандидата для покупки
```bash
skill analyze:quick --ticker=GAZP
skill analyze:fundamental --ticker=GAZP
```

## Форматы вывода
```bash
skill portfolio:analyze --format=table  # по умолчанию
skill portfolio:analyze --format=json
skill portfolio:analyze --format=csv
```

## Интеграция

Команда вызывается через vendor binary:
```bash
./vendor/bin/skill portfolio:analyze
```

## Справочник API

OpenAPI: https://russianinvestments.github.io/investAPI/swagger-ui/openapi.yaml
