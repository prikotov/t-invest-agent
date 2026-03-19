# T-Invest Skill

Навык для работы с портфелем через T-Invest API.

## Команды

### Портфель
```bash
t-invest portfolio:show
t-invest portfolio:show --ticker=SBER
```
Возвращает: позиции, количество, средняя цена, текущая цена, доходность

### Счета
```bash
t-invest accounts:list
```
Возвращает: список счетов с ID и статусом

### Рыночные данные
```bash
t-invest market:prices --figi=BBG004730N88
t-invest market:candles --figi=BBG004730N88 --from=2024-01-01 --to=2024-01-31
```
Возвращает: текущие цены, исторические свечи

### Фундаментальные данные
```bash
t-invest instruments:fundamentals --ticker=SBER
```
Возвращает: P/E, P/B, ROE, дивиденды

### История операций
```bash
t-invest operations:history --from=2024-01-01 --to=2024-01-31
```
Возвращает: список операций за период

## Типовые сценарии

### Еженедельный мониторинг
```bash
t-invest portfolio:show
```
Проверить: доходность позиций, отклонения > 5%

### Анализ кандидата для покупки
```bash
t-invest instruments:fundamentals --ticker=GAZP
t-invest market:candles --figi=<FIGI> --from=2024-01-01
```

## Интеграция

Команда вызывается через binary:
```bash
./bin/t-invest portfolio:show
```

## Справочник API

OpenAPI: https://russianinvestments.github.io/investAPI/swagger-ui/openapi.yaml
