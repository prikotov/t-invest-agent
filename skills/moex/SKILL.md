# MOEX Skill

Навык для работы с Московской Биржей через MOEX ISS API.

## Команды

### Спецификация инструмента
```bash
moex security:specification SBER
```
Возвращает: ISIN, List Level, Type, Issue Size

### Рыночные данные
```bash
moex security:trade-data SBER
```
Возвращает: Last, Open/High/Low, Volume Today

### Итоги торгов
```bash
moex security:aggregates SBER
```
Возвращает: объёмы по рынкам (акции, РЕПО)

### Индексы
```bash
moex security:indices SBER
moex index:weight SBER IMOEX
moex index:composition IMOEX
```
Возвращает: список индексов, вес в индексе, состав индекса

## Типовые сценарии

### Оценка ликвидности
```bash
moex security:aggregates SBER | grep "Рынок акций" -A5
```
| Объём в день | Ликвидность | Действие |
|--------------|-------------|----------|
| > 3 млрд     | Высокая     | Можно торговать крупно |
| 1-3 млрд     | Средняя     | Лимитные заявки |
| < 1 млрд     | Низкая      | Осторожно |

### Проверка индексной значимости
```bash
moex security:indices SBER
```
- Входит в IMOEX → низкий риск ликвидности
- `Till` дата в прошлом → исключена из индекса

### Полный анализ бумаги
```bash
moex security:specification $TICKER
moex security:trade-data $TICKER
moex security:indices $TICKER
moex security:aggregates $TICKER
```

## Интеграция

Команда вызывается через vendor binary:
```bash
./vendor/bin/moex security:specification SBER
```

## Справочник API

MOEX ISS API: https://iss.moex.com/iss/reference/
