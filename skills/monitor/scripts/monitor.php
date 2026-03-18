#!/usr/bin/env php
<?php

$dataDir = __DIR__ . '/../../../data/monitors';
$command = $argv[1] ?? 'list';
$args = array_slice($argv, 2);

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

function parseArgs(array $args): array
{
    $parsed = ['positional' => []];
    foreach ($args as $arg) {
        if (str_starts_with($arg, '--')) {
            $parts = explode('=', substr($arg, 2), 2);
            $parsed[$parts[0]] = $parts[1] ?? true;
        } else {
            $parsed['positional'][] = $arg;
        }
    }
    return $parsed;
}

$parsedArgs = parseArgs($args);

match ($command) {
    'create' => createMonitor($parsedArgs, $dataDir),
    'list' => listMonitors($parsedArgs, $dataDir),
    'check' => checkMonitors($parsedArgs, $dataDir),
    'run' => runMonitor($parsedArgs, $dataDir),
    'delete' => deleteMonitor($parsedArgs, $dataDir),
    default => listMonitors($parsedArgs, $dataDir),
};

function createMonitor(array $args, string $dataDir): void
{
    $type = $args['positional'][0] ?? $args['type'] ?? null;

    if (!$type || !in_array($type, ['price', 'schedule'])) {
        echo "Usage: php monitor.php create price|schedule [options]\n";
        echo "  price:    --ticker=TICKER --level=PRICE --direction=UP|DOWN\n";
        echo "  schedule: --cron=EXPR --prompt=PROMPT\n";
        exit(1);
    }

    $id = 'monitor-' . str_pad(count(glob($dataDir . '/*.json')) + 1, 3, '0', STR_PAD_LEFT);

    if ($type === 'price') {
        $monitor = createPriceMonitor($args, $id);
    } else {
        $monitor = createScheduleMonitor($args, $id);
    }

    file_put_contents($dataDir . '/' . $id . '.json', json_encode($monitor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "Monitor created: $id\n\n";
    echo json_encode($monitor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    if (!empty($monitor['ticker'])) {
        echo "\nНе является инвестиционной рекомендацией.\n";
        echo "{$monitor['ticker']}\n";
    }
}

function createPriceMonitor(array $args, string $id): array
{
    $ticker = $args['ticker'] ?? null;
    $level = isset($args['level']) ? (float)$args['level'] : null;
    $direction = strtoupper($args['direction'] ?? 'UP');

    if (!$ticker || !$level) {
        echo "Error: --ticker and --level required for price monitor\n";
        exit(1);
    }

    if (!in_array($direction, ['UP', 'DOWN'])) {
        echo "Error: --direction must be UP or DOWN\n";
        exit(1);
    }

    return [
        'id' => $id,
        'type' => 'price',
        'ticker' => strtoupper($ticker),
        'level' => $level,
        'direction' => $direction,
        'recipe_id' => $args['recipe'] ?? null,
        'action' => $args['action'] ?? null,
        'status' => 'ACTIVE',
        'created_at' => date('c'),
        'triggered_at' => null,
    ];
}

function createScheduleMonitor(array $args, string $id): array
{
    $cron = $args['cron'] ?? null;
    $prompt = $args['prompt'] ?? null;

    if (!$cron || !$prompt) {
        echo "Error: --cron and --prompt required for schedule monitor\n";
        exit(1);
    }

    return [
        'id' => $id,
        'type' => 'schedule',
        'cron' => $cron,
        'prompt' => $prompt,
        'status' => 'ACTIVE',
        'created_at' => date('c'),
        'last_run' => null,
        'next_run' => date('c', strtotime('+1 hour')),
    ];
}

function listMonitors(array $args, string $dataDir): void
{
    $typeFilter = $args['type'] ?? null;

    $files = glob($dataDir . '/*.json');
    if (empty($files)) {
        echo "No monitors found.\n";
        return;
    }

    $monitors = [];
    foreach ($files as $file) {
        $monitor = json_decode(file_get_contents($file), true);
        if ($typeFilter && $monitor['type'] !== $typeFilter) {
            continue;
        }
        $monitors[] = $monitor;
    }

    if (empty($monitors)) {
        echo "No monitors found";
        if ($typeFilter) echo " with type: $typeFilter";
        echo ".\n";
        return;
    }

    usort($monitors, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

    $priceMonitors = array_filter($monitors, fn($m) => $m['type'] === 'price');
    $scheduleMonitors = array_filter($monitors, fn($m) => $m['type'] === 'schedule');

    if (!empty($priceMonitors)) {
        echo "## Price Monitors\n\n";
        echo "| ID | Ticker | Level | Dir | Status | Recipe |\n";
        echo "|----|--------|-------|-----|--------|--------|\n";
        foreach ($priceMonitors as $m) {
            printf(
                "| %s | %s | %s | %s | %s | %s |\n",
                $m['id'],
                $m['ticker'],
                $m['level'],
                $m['direction'],
                $m['status'],
                $m['recipe_id'] ?? '-'
            );
        }
        echo "\n";
    }

    if (!empty($scheduleMonitors)) {
        echo "## Schedule Monitors\n\n";
        echo "| ID | Cron | Prompt | Status |\n";
        echo "|----|------|--------|--------|\n";
        foreach ($scheduleMonitors as $m) {
            printf(
                "| %s | %s | %s | %s |\n",
                $m['id'],
                $m['cron'],
                $m['prompt'],
                $m['status']
            );
        }
        echo "\n";
    }

    echo "Total: " . count($monitors) . " monitors\n";
}

function checkMonitors(array $args, string $dataDir): void
{
    $files = glob($dataDir . '/*.json');
    if (empty($files)) {
        echo "No monitors to check.\n";
        return;
    }

    $triggered = [];

    foreach ($files as $file) {
        $monitor = json_decode(file_get_contents($file), true);

        if ($monitor['status'] !== 'ACTIVE') {
            continue;
        }

        if ($monitor['type'] === 'price') {
            if (checkPriceMonitor($monitor)) {
                $triggered[] = $monitor;
                $monitor['status'] = 'TRIGGERED';
                $monitor['triggered_at'] = date('c');
                file_put_contents($file, json_encode($monitor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        }
    }

    if (empty($triggered)) {
        echo "No monitors triggered.\n";
        return;
    }

    echo "## Triggered Monitors\n\n";
    foreach ($triggered as $m) {
        printf(
            "**%s**: %s %s %.2f\n",
            $m['id'],
            $m['ticker'],
            $m['direction'] === 'UP' ? '≥' : '≤',
            $m['level']
        );
        if (!empty($m['recipe_id'])) {
            echo "  Recipe: {$m['recipe_id']}\n";
        }
        echo "\n";
    }

    $tickers = array_unique(array_column(array_filter($triggered, fn($m) => $m['type'] === 'price'), 'ticker'));
    if (!empty($tickers)) {
        echo "\nНе является инвестиционной рекомендацией.\n";
        echo implode(', ', $tickers) . "\n";
    }
}

function checkPriceMonitor(array $monitor): bool
{
    $ticker = $monitor['ticker'];
    $url = "https://iss.moex.com/iss/engines/stock/markets/shares/securities/{$ticker}.json?iss.meta=off&iss.only=marketdata";
    $json = @file_get_contents($url);
    if (!$json) return false;

    $data = json_decode($json, true);
    $currentPrice = (float)($data['marketdata']['data'][0][1] ?? 0);
    if ($currentPrice <= 0) return false;

    if ($monitor['direction'] === 'UP') {
        return $currentPrice >= $monitor['level'];
    } else {
        return $currentPrice <= $monitor['level'];
    }
}

function runMonitor(array $args, string $dataDir): void
{
    $id = $args['positional'][0] ?? null;

    if (!$id) {
        echo "Usage: php monitor.php run <id>\n";
        exit(1);
    }

    $file = $dataDir . '/' . $id . '.json';
    if (!file_exists($file)) {
        echo "Monitor not found: $id\n";
        exit(1);
    }

    $monitor = json_decode(file_get_contents($file), true);

    echo "# Running monitor: $id\n\n";
    echo "Type: {$monitor['type']}\n";

    if ($monitor['type'] === 'price') {
        echo "Ticker: {$monitor['ticker']}\n";
        echo "Level: {$monitor['level']}\n";
        echo "Direction: {$monitor['direction']}\n";
        echo "\nTo check: ./vendor/bin/moex security:trade-data {$monitor['ticker']}\n";
    } elseif ($monitor['type'] === 'schedule') {
        echo "Prompt: {$monitor['prompt']}\n";
    }

    $monitor['last_run'] = date('c');
    file_put_contents($file, json_encode($monitor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "\nMonitor executed at: {$monitor['last_run']}\n";

    if (!empty($monitor['ticker'])) {
        echo "\nНе является инвестиционной рекомендацией.\n";
        echo "{$monitor['ticker']}\n";
    }
}

function deleteMonitor(array $args, string $dataDir): void
{
    $id = $args['positional'][0] ?? null;

    if (!$id) {
        echo "Usage: php monitor.php delete <id>\n";
        exit(1);
    }

    $file = $dataDir . '/' . $id . '.json';
    if (!file_exists($file)) {
        echo "Monitor not found: $id\n";
        exit(1);
    }

    $monitor = json_decode(file_get_contents($file), true);
    unlink($file);

    echo "Monitor deleted: $id\n";

    if (!empty($monitor['ticker'])) {
        echo "\nНе является инвестиционной рекомендацией.\n";
        echo "{$monitor['ticker']}\n";
    }
}
