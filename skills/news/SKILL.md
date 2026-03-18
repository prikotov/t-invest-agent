# News Skill

Навык для агрегации и поиска финансовых новостей из RSS-лент.

## Источники

- Interfax, TASS, RIA Novosti, PRIME, RBC, Kommersant

## Команды

### Получение новостей
```bash
news news:fetch
news news:fetch --ticker SBER
news news:fetch --ticker SBER --ticker LKOH
```

### Кэширование
```bash
news news:cache    # Загрузить и кэшировать
```

### Поиск по кэшу
```bash
news news:search "Сбербанк"
news news:search "нефть"
```

### Информация
```bash
news news:sources  # Доступные источники
```

## Типовые сценарии

### Новости по тикеру
```bash
news news:fetch --ticker SBER
```
Автоматически расширяет: SBER → "Сбербанк", "Sber", "SBER"

### Новости по теме
```bash
news news:fetch --search "нефть"
```

### Поиск в архиве
```bash
news news:search "Сбербанк"
```

## Интеграция

Команда вызывается через vendor binary:
```bash
./vendor/bin/news news:fetch --ticker SBER
```

## Поддерживаемые тикеры

SBER, GAZP, LKOH, NVTK, ROSN, GMKN, YNDX, VTBR, TCSG, MOEX и др.
