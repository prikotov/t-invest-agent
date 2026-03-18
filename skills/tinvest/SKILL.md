# T-Invest Skill

Навык для работы с портфелем через T-Invest API.

## Команды

### Портфель
```bash
skill portfolio:show              # Весь портфель
skill portfolio:show -t SBER      # По тикеру
```
Возвращает: позиции, количество, средняя цена, доходность, текущая цена

### Счета
```bash
skill accounts:list
```
Возвращает: список счетов пользователя

### Операции
```bash
skill operations:history
```
Возвращает: история операций

### Рыночные цены
```bash
skill market:prices SBER LKOH GAZP
```
Возвращает: последние цены по инструментам

### Исторические свечи
```bash
skill market:candles SBER --from 2024-01-01 --to 2024-12-31
```
Возвращает: OHLCV данные

### Фундаментальные показатели
```bash
skill instruments:fundamentals SBER LKOH
```
Возвращает: P/E, P/B, дивиденды и другие метрики

## Типовые сценарии

### Анализ портфеля
```bash
skill portfolio:show
skill market:prices SBER LKOH GAZP
```

### Анализ кандидата
```bash
skill instruments:fundamentals GAZP
skill market:candles GAZP --from 2024-01-01
```

## Интеграция

Команда вызывается через vendor binary:
```bash
./vendor/bin/skill portfolio:show
```

## Справочник API

OpenAPI: https://russianinvestments.github.io/investAPI/swagger-ui/openapi.yaml
