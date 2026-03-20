---
name: calc
description: Выполнение финансовых расчётов на PHP — R:R, размер позиции, статистика.
---

# Calc

## Когда использовать

- Расчёт Risk/Reward для сделки
- Расчёт размера позиции по риску
- Статистический анализ (среднее, стандартное отклонение, корреляция)
- Любые финансовые вычисления

## Как использовать

**Простой расчёт (одна строка):**

```bash
php -r 'echo 260 * 1.18;'
```

**Сложный расчёт — создать папку и скрипт:**

```bash
# Создать папку для расчёта
mkdir -p data/_tmp_scripts/stddev_sber

# Создать скрипт
php -r 'file_put_contents("data/_tmp_scripts/stddev_sber/stddev_sber.php", "<?php
  // Расчёт среднего и стандартного отклонения для цен SBER
  \$prices = [260, 265, 270, 255, 250];
  \$avg = array_sum(\$prices) / count(\$prices);
  \$std = sqrt(array_sum(array_map(fn(\$p) => (\$p - \$avg) ** 2, \$prices)) / count(\$prices));
  echo json_encode([\"avg\" => \$avg, \"std\" => \$std]);
");'

# Выполнить с выводом в файл (с временной меткой)
php data/_tmp_scripts/stddev_sber/stddev_sber.php > data/_tmp_scripts/stddev_sber/stddev_sber_$(date +%Y%m%d).json

# Прочитать результат
cat data/_tmp_scripts/stddev_sber/stddev_sber_20240319.json
```

## Организация файлов

Структура: `data/_tmp_scripts/<calc_name>/<calc_name>.php` и `data/_tmp_scripts/<calc_name>/<calc_name>_YYYYMMDD.json`

Скрипт и результаты хранятся в одной папке.

**Именование:**
- `stddev_sber` — стандартное отклонение для SBER
- `position_size_gazp` — размер позиции для GAZP
- `correlation_portfolio` — корреляция в портфеле

Имена должны отражать суть расчёта для возможности переиспользования.

## Примеры расчётов

### Risk/Reward

```php
$entry = 260;
$target = 300;
$stop = 245;
$rr = ($target - $entry) / ($entry - $stop); // = 2.67
```

### Размер позиции

```php
$account = 1000000;  // руб
$riskPct = 0.02;     // 2% риска
$entry = 260;
$stop = 245;
$riskPerShare = $entry - $stop;       // 15 руб
$maxRisk = $account * $riskPct;       // 20000 руб
$shares = (int) ($maxRisk / $riskPerShare); // 1333 акций
```

## Очистка

После использования файлы в `data/_tmp_scripts/` можно удалить.
