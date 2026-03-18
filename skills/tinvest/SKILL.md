# T-Invest Skill

Навык для работы с портфелем через T-Invest API.

## Команды

### Портфель
```bash
./vendor/bin/t-invest portfolio:show              # Весь портфель
./vendor/bin/t-invest portfolio:show -t SBER      # По тикеру
```
Возвращает: позиции, количество, средняя цена, доходность, текущая цена

### Счета
```bash
./vendor/bin/t-invest accounts:list
```
Возвращает: список счетов пользователя

### Операции
```bash
./vendor/bin/t-invest operations:history
```
Возвращает: история операций

### Рыночные цены
```bash
./vendor/bin/t-invest market:prices SBER LKOH GAZP
```
Возвращает: последние цены по инструментам

### Исторические свечи
```bash
./vendor/bin/t-invest market:candles SBER --from 2024-01-01 --to 2024-12-31
```
Возвращает: OHLCV данные

### Фундаментальные показатели
```bash
./vendor/bin/t-invest instruments:fundamentals SBER LKOH
```
Возвращает: P/E, P/B, дивиденды и другие метрики

## Типовые сценарии

### Анализ портфеля
```bash
./vendor/bin/t-invest portfolio:show
./vendor/bin/t-invest market:prices SBER LKOH GAZP
```

### Анализ кандидата
```bash
./vendor/bin/t-invest instruments:fundamentals GAZP
./vendor/bin/t-invest market:candles GAZP --from 2024-01-01
```

## Интеграция

Команда вызывается через vendor binary:
```bash
./vendor/bin/t-invest portfolio:show
```

## Справочник API

OpenAPI: https://russianinvestments.github.io/investAPI/swagger-ui/openapi.yaml
