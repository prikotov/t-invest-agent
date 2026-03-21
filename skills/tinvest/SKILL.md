# T-Invest Skill

Навык для работы с портфелем через T-Invest API.

## Команды

### Портфель

```bash
./vendor/bin/t-invest portfolio:show [options]
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
./vendor/bin/t-invest portfolio:show
./vendor/bin/t-invest portfolio:show --instrument=SBER
./vendor/bin/t-invest portfolio:show --sort=yield --order=desc --limit=5 --format=json
./vendor/bin/t-invest portfolio:show --sort=value --order=desc --limit=3
```

Возвращает: позиции, количество, средняя цена, текущая цена, доходность (%).

### Счета

```bash
./vendor/bin/t-invest accounts:list
```
Возвращает: список счетов с ID и статусом

### Рыночные данные
```bash
./vendor/bin/t-invest market:prices SBER GAZP LKOH
./vendor/bin/t-invest market:candles --ticker=SBER --from="-7 days"
./vendor/bin/t-invest market:orderbook SBER --depth=20
```
Возвращает: текущие цены, исторические свечи, стакан заявок.

### Фундаментальные данные
```bash
./vendor/bin/t-invest instruments:fundamentals SBER GAZP
./vendor/bin/t-invest instruments:resolve BBG004730N88
```
Возвращает: P/E, P/B, ROE, дивидендная доходность

### История операций
```bash
./vendor/bin/t-invest operations:history --from=2024-01-01 --to=2024-01-31
./vendor/bin/t-invest operations:history --sort=payment --order=desc --limit=10 --format=json
```
Возвращает: список операций за период

### Резолв инструментов
```bash
./vendor/bin/t-invest instruments:resolve BBG004730N88
./vendor/bin/t-invest instruments:resolve SBER
```
Возвращает: FIGI, тикер, UID, ISIN, имя, classCode, currency, lot

## Типовые сценарии

### Еженедельный мониторинг
```bash
./vendor/bin/t-invest portfolio:show
```
Проверить: доходность позиций, отклонения > 5%

### Анализ кандидата для покупки
```bash
./vendor/bin/t-invest instruments:fundamentals --ticker=GAZP
./vendor/bin/t-invest market:candles --ticker=GAZP --from=2024-01-01
./vendor/bin/t-invest instruments:resolve <FIGI>
```

### Топ позиций по доходности
```bash
./vendor/bin/t-invest portfolio:show --sort=yield --order=desc --limit=5 --format=json
```

### Топ операций по сумме
```bash
./vendor/bin/t-invest operations:history --sort=payment --order=desc --limit=10 --format=json
```

## Интеграция

Команда вызывается через binary:
```bash
./bin/t-invest portfolio:show
```

## Справочник API

OpenAPI: https://russianinvestments.github.io/investAPI/swagger-ui/openapi.yaml
