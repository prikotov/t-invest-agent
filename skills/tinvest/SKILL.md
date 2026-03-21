# T-Invest Skill

Навык для работы с портфелем через T-Invest API.

## Команды

### Портфель

```bash
t-invest portfolio:show [options]
```

Опции:

| Опция        | Сокращение | Описание                  | Значения                   | По умолчанию |
|--------------|------------|---------------------------|----------------------------|--------------|
| --instrument | -i         | Фильтр по тикеру или FIGI | SBER, BBG004730N88         | все          |
| --sort       | -s         | Сортировка                | ticker, yield, value, type | ticker       |
| --order      | -o         | Порядок сортировки        | asc, desc                  | asc          |
| --limit      | -l         | Ограничить число позиций  | число                      | 0 (все)      |
| --format     | -f         | Формат вывода             | table, json                | table        |

Примеры:

```bash
t-invest portfolio:show
t-invest portfolio:show --instrument=SBER
t-invest portfolio:show --sort=yield --order=desc --limit=5 --format=json
t-invest portfolio:show --sort=value --order=desc --limit=3
```

Возвращает: позиции, количество, средняя цена, текущая цена, доходность (%).

### Счета

```bash
t-invest accounts:list
```
Возвращает: список счетов с ID и статусом

### Рыночные данные
```bash
t-invest market:prices SBER GAZP LKOH
t-invest market:candles SBER --from="-7 days"
t-invest market:orderbook SBER --depth=20
```
Возвращает: текущие цены, исторические свечи, стакан заявок.

### Фундаментальные данные
```bash
t-invest instruments:fundamentals SBER GAZP
t-invest instruments:resolve BBG004730N88
```
Возвращает: P/E, P/B, ROE, дивидендная доходность

### История операций
```bash
t-invest operations:history --from=2024-01-01 --to=2024-01-31
t-invest operations:history --sort=payment --order=desc --limit=10 --format=json
```
Возвращает: список операций за период

### Резолв инструментов
```bash
t-invest instruments:resolve BBG004730N88
t-invest instruments:resolve SBER
```
Возвращает: FIGI, тикер, UID, ISIN, имя, classCode, currency, lot

## Типовые сценарии

### Еженедельный мониторинг
```bash
t-invest portfolio:show
```
Проверить: доходность позиций, отклонения > 5%

### Анализ кандидата для покупки
```bash
t-invest instruments:fundamentals --ticker=GAZP
t-invest market:candles GAZP --from=2024-01-01
t-invest instruments:resolve <FIGI>
```

### Топ позиций по доходности
```bash
t-invest portfolio:show --sort=yield --order=desc --limit=5 --format=json
```

### Топ операций по сумме
```bash
t-invest operations:history --sort=payment --order=desc --limit=10 --format=json
```

## Интеграция

Команда вызывается через binary:
```bash
./bin/t-invest portfolio:show
```

## Справочник API

OpenAPI: https://russianinvestments.github.io/investAPI/swagger-ui/openapi.yaml
