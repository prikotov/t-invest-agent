#!/usr/bin/env php
<?php

$dataDir = __DIR__ . '/../../../data/memory';
$command = $argv[1] ?? 'profile';
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

function getDefaultProfile(): array
{
    return [
        'risk_tolerance' => 'moderate',
        'horizon' => 'long-term',
        'style' => 'value',
        'favorite_sectors' => [],
        'avoid_sectors' => [],
        'max_position_pct' => 10,
    ];
}

$parsedArgs = parseArgs($args);

match ($command) {
    'get' => getMemory($parsedArgs, $dataDir),
    'set' => setMemory($parsedArgs, $dataDir),
    'update' => updateMemory($parsedArgs, $dataDir),
    'profile' => showProfile($dataDir),
    'clear' => clearMemory($dataDir),
    default => showProfile($dataDir),
};

function getMemory(array $args, string $dataDir): void
{
    $key = $args['positional'][0] ?? null;

    if (!$key) {
        echo "Usage: php memory.php get <key>\n";
        exit(1);
    }

    $file = $dataDir . '/user.json';
    if (!file_exists($file)) {
        echo "Memory is empty. Use 'php memory.php profile' to see defaults.\n";
        exit(1);
    }

    $memory = json_decode(file_get_contents($file), true);

    if (!isset($memory[$key])) {
        echo "Key not found: $key\n";
        echo "Available keys: " . implode(', ', array_keys($memory)) . "\n";
        exit(1);
    }

    $value = $memory[$key];
    if (is_array($value)) {
        echo json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    } else {
        echo "$key = $value\n";
    }
}

function setMemory(array $args, string $dataDir): void
{
    $key = $args['positional'][0] ?? null;
    $value = $args['positional'][1] ?? null;

    if (!$key || $value === null) {
        echo "Usage: php memory.php set <key> <value>\n";
        exit(1);
    }

    $file = $dataDir . '/user.json';
    $memory = file_exists($file) ? json_decode(file_get_contents($file), true) : getDefaultProfile();

    if (in_array($value, ['true', 'false'])) {
        $value = $value === 'true';
    } elseif (is_numeric($value)) {
        $value = strpos($value, '.') !== false ? (float)$value : (int)$value;
    } elseif (str_starts_with($value, '[') || str_starts_with($value, '{')) {
        $decoded = json_decode($value, true);
        if ($decoded !== null) $value = $decoded;
    }

    $memory[$key] = $value;
    $memory['updated_at'] = date('c');

    file_put_contents($file, json_encode($memory, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $displayValue = is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value;
    echo "Set: $key = $displayValue\n";
}

function updateMemory(array $args, string $dataDir): void
{
    $file = $dataDir . '/user.json';
    $memory = file_exists($file) ? json_decode(file_get_contents($file), true) : getDefaultProfile();

    $updated = [];

    $mappings = [
        'risk' => 'risk_tolerance',
        'horizon' => 'horizon',
        'style' => 'style',
        'sectors' => 'favorite_sectors',
        'max-pos' => 'max_position_pct',
    ];

    foreach ($mappings as $arg => $field) {
        if (isset($args[$arg])) {
            $value = $args[$arg];
            if ($field === 'favorite_sectors' || $field === 'avoid_sectors') {
                $value = array_map('trim', explode(',', $value));
            } elseif ($field === 'max_position_pct') {
                $value = (int)$value;
            }
            $memory[$field] = $value;
            $updated[] = "$field = " . (is_array($value) ? json_encode($value) : $value);
        }
    }

    if (empty($updated)) {
        echo "Usage: php memory.php update [--risk=...] [--horizon=...] [--style=...] [--sectors=...] [--max-pos=...]\n";
        exit(1);
    }

    $memory['updated_at'] = date('c');
    file_put_contents($file, json_encode($memory, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "Profile updated:\n";
    foreach ($updated as $u) {
        echo "  - $u\n";
    }
}

function showProfile(string $dataDir): void
{
    $file = $dataDir . '/user.json';

    $profile = file_exists($file) 
        ? json_decode(file_get_contents($file), true) 
        : getDefaultProfile();

    $labels = [
        'risk_tolerance' => 'Risk Tolerance',
        'horizon' => 'Investment Horizon',
        'style' => 'Investment Style',
        'favorite_sectors' => 'Favorite Sectors',
        'avoid_sectors' => 'Avoid Sectors',
        'max_position_pct' => 'Max Position %',
        'positions_last_sync' => 'Last Sync',
        'updated_at' => 'Last Updated',
    ];

    $descriptions = [
        'conservative' => 'Низкий риск, стабильный доход',
        'moderate' => 'Средний риск, баланс роста и дохода',
        'aggressive' => 'Высокий риск, максимальный рост',
        'short-term' => 'До 1 года',
        'medium-term' => '1-3 года',
        'long-term' => '3+ лет',
        'value' => 'Недооценённые компании',
        'growth' => 'Быстрорастущие компании',
        'dividend' => 'Дивидендные акции',
        'momentum' => 'Трендовые движения',
    ];

    echo file_exists($file) ? "# User Profile\n\n" : "# User Profile (defaults)\n\n";

    foreach ($labels as $key => $label) {
        if (!isset($profile[$key])) continue;

        $value = $profile[$key];
        $desc = '';

        if (is_array($value)) {
            $value = empty($value) ? '-' : implode(', ', $value);
        } else {
            $desc = $descriptions[$value] ?? '';
            if ($key === 'max_position_pct') $desc = 'Макс. доля одной позиции';
        }

        echo "**$label:** $value";
        if ($desc) echo " ($desc)";
        echo "\n";
    }

    if (!file_exists($file)) {
        echo "\nTo customize, use: php memory.php set <key> <value>\n";
    }
}

function clearMemory(string $dataDir): void
{
    $file = $dataDir . '/user.json';

    if (!file_exists($file)) {
        echo "Memory is already empty.\n";
        return;
    }

    unlink($file);
    echo "Memory cleared. Profile will use defaults.\n";
}
