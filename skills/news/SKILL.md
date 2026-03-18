# News Skill

Навык для агрегации и поиска финансовых новостей из RSS-лент.

## Источники

- Interfax, TASS, RIA Novosti, PRIME, RBC, Kommersant

## Команды

### Получение новостей
```bash
news news:fetch
news news:fetch --source=interfax --source=tass
news news:fetch --search "нефть" --search "золото"
news news:fetch --ticker SBER --ticker GAZP
news news:fetch --category "Экономика"
news news:fetch --limit=10 --format=json
```

### Кэширование
```bash
news news:cache                    # Загрузить и кэшировать
news news:cache --source=interfax  # Только Interfax
news news:cache --clear=30         # Очистить старше 30 дней
```

### Поиск по кэшу
```bash
news news:search "Сбербанк"
news news:search "нефть" --category "Экономика"
news news:search "" --source=interfax --days=3
```

### Информация
```bash
news news:cache-stats  # Статистика кэша
news news:sources      # Доступные источники
```

## Типовые сценарии

### Новости по тикеру
```bash
news news:fetch --ticker SBER
```
Автоматически расширяет: SBER → "Сбербанк", "Sber", "SBER"

### Новости по теме
```bash
news news:fetch --search "нефть" --search "ОПЕК+"
```

### Поиск в архиве
```bash
news news:search "Сбербанк" --days=7 --category "Экономика"
```

## Форматы вывода
```bash
news news:fetch --format=table  # по умолчанию
news news:fetch --format=json
```

## Интеграция

Команда вызывается через vendor binary:
```bash
./vendor/bin/news news:fetch --ticker SBER
```

## Поддерживаемые тикеры

SBER, GAZP, LKOH, NVTK, ROSN, GMKN, YNDX, VTBR, TCSG, MOEX и др.
