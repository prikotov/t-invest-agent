# Управление навыками

## Профили навыков

Профили позволяют быстро активировать наборы навыков:

```bash
# Список профилей
php bin/agent skill:profile

# Применить профиль
php bin/agent skill:profile analysis
```

## Доступные профили

Конфигурация: `config/skills.yaml`

| Профиль | Навыки |
|---------|--------|
| `default` | tinvest-portfolio-analysis, moex-ticker-analysis, calc, tinvest-trading-calendar |
| `analysis` | tinvest, moex, news, tinvest-portfolio-analysis, moex-ticker-analysis, calc, tinvest-trading-calendar, moex-trading-calendar |
| `trading` | tinvest, moex, calc, tinvest-trading-calendar, moex-trading-calendar |
| `news` | news, moex-ticker-analysis |
| `minimal` | tinvest |

## Команды

```bash
php bin/agent skill:list              # список всех навыков
php bin/agent skill:manage            # интерактивное управление
php bin/agent skill:enable <name>     # включить навык
php bin/agent skill:disable <name>    # выключить навык
php bin/agent skill:profile           # список профилей
php bin/agent skill:profile <name>    # применить профиль
```

## Добавление профиля

Отредактируй `config/skills.yaml`:

```yaml
skills:
  my-profile:
    - skill1
    - skill2
```
