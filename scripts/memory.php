#!/usr/bin/env php
<?php

declare(strict_types=1);

const DATA_DIR = __DIR__ . '/../data/memory';
const USER_FILE = DATA_DIR . '/user.json';

const DEFAULTS = [
    'risk_tolerance' => 'moderate',
    'horizon' => 'long-term',
    'style' => 'value',
    'favorite_sectors' => [],
    'avoid_sectors' => [],
    'max_position_pct' => 10,
    'positions' => [],
    'positions_updated_at' => null,
];

function main(array $argv): int
{
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0755, true);
    }
    
    $command = $argv[1] ?? 'profile';
    
    return match ($command) {
        'profile' => profile(),
        'get' => get($argv[2] ?? null),
        'set' => set(array_slice($argv, 2)),
        'update' => update(array_slice($argv, 2)),
        'sync-portfolio' => sync_portfolio(),
        'positions' => show_positions(),
        'clear' => clear(),
        default => help(),
    };
}

function help(): int
{
    echo "Usage: memory.php <command> [options]\n\n";
    echo "Commands:\n";
    echo "  profile                        Show user profile\n";
    echo "  get <key>                      Get a value\n";
    echo "  set <key> <value>              Set a value\n";
    echo "  update [--risk=...] [--horizon=...] [--style=...] [--sectors=...] [--max-pos=...]\n";
    echo "  sync-portfolio                 Sync positions from T-Invest portfolio\n";
    echo "  positions                      Show synced positions\n";
    echo "  clear                          Reset to defaults\n";
    return 0;
}

function load(): array
{
    if (!file_exists(USER_FILE)) {
        return DEFAULTS;
    }
    
    $data = json_decode(file_get_contents(USER_FILE), true);
    return array_merge(DEFAULTS, $data);
}

function save(array $data): void
{
    $data['updated_at'] = date('c');
    file_put_contents(USER_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function profile(): int
{
    $data = load();
    
    echo "=== User Profile ===\n\n";
    echo "Risk Tolerance: {$data['risk_tolerance']}\n";
    echo "Horizon: {$data['horizon']}\n";
    echo "Style: {$data['style']}\n";
    echo "Favorite Sectors: " . (empty($data['favorite_sectors']) ? 'none' : implode(', ', $data['favorite_sectors'])) . "\n";
    echo "Avoid Sectors: " . (empty($data['avoid_sectors']) ? 'none' : implode(', ', $data['avoid_sectors'])) . "\n";
    echo "Max Position: {$data['max_position_pct']}%\n";
    
    if ($data['updated_at'] ?? null) {
        echo "\nLast updated: {$data['updated_at']}\n";
    } else {
        echo "\n(using defaults)\n";
    }
    
    return 0;
}

function get(?string $key): int
{
    if (!$key) {
        echo "Error: key required\n";
        return 1;
    }
    
    $data = load();
    
    if (!array_key_exists($key, $data)) {
        echo "Error: unknown key '$key'\n";
        echo "Valid keys: " . implode(', ', array_keys(DEFAULTS)) . "\n";
        return 1;
    }
    
    $value = $data[$key];
    
    if (is_array($value)) {
        echo json_encode($value) . "\n";
    } else {
        echo $value . "\n";
    }
    
    return 0;
}

function set(array $args): int
{
    if (count($args) < 2) {
        echo "Error: key and value required\n";
        return 1;
    }
    
    $key = $args[0];
    $value = $args[1];
    
    $data = load();
    
    if (!array_key_exists($key, DEFAULTS)) {
        echo "Error: unknown key '$key'\n";
        echo "Valid keys: " . implode(', ', array_keys(DEFAULTS)) . "\n";
        return 1;
    }
    
    if (in_array($key, ['favorite_sectors', 'avoid_sectors'])) {
        $decoded = json_decode($value, true);
        $data[$key] = is_array($decoded) ? $decoded : array_map('trim', explode(',', $value));
    } elseif ($key === 'max_position_pct') {
        $data[$key] = (int) $value;
    } else {
        $data[$key] = $value;
    }
    
    save($data);
    
    echo "Set {$key} = " . (is_array($data[$key]) ? json_encode($data[$key]) : $data[$key]) . "\n";
    
    return 0;
}

function update(array $args): int
{
    $parsed = parse_args($args);
    $data = load();
    
    if (empty($parsed)) {
        echo "Error: at least one option required\n";
        return 1;
    }
    
    if (isset($parsed['risk'])) {
        $valid = ['conservative', 'moderate', 'aggressive'];
        if (!in_array($parsed['risk'], $valid)) {
            echo "Error: --risk must be one of: " . implode(', ', $valid) . "\n";
            return 1;
        }
        $data['risk_tolerance'] = $parsed['risk'];
    }
    
    if (isset($parsed['horizon'])) {
        $valid = ['short-term', 'medium-term', 'long-term'];
        if (!in_array($parsed['horizon'], $valid)) {
            echo "Error: --horizon must be one of: " . implode(', ', $valid) . "\n";
            return 1;
        }
        $data['horizon'] = $parsed['horizon'];
    }
    
    if (isset($parsed['style'])) {
        $valid = ['value', 'growth', 'dividend', 'momentum'];
        if (!in_array($parsed['style'], $valid)) {
            echo "Error: --style must be one of: " . implode(', ', $valid) . "\n";
            return 1;
        }
        $data['style'] = $parsed['style'];
    }
    
    if (isset($parsed['sectors'])) {
        $data['favorite_sectors'] = array_map('trim', explode(',', $parsed['sectors']));
    }
    
    if (isset($parsed['avoid'])) {
        $data['avoid_sectors'] = array_map('trim', explode(',', $parsed['avoid']));
    }
    
    if (isset($parsed['max-pos'])) {
        $data['max_position_pct'] = (int) $parsed['max-pos'];
    }
    
    save($data);
    
    echo "Profile updated.\n";
    profile();
    
    return 0;
}

function clear(): int
{
    if (file_exists(USER_FILE)) {
        unlink(USER_FILE);
    }
    echo "Memory cleared. Using defaults.\n";
    return 0;
}

function sync_portfolio(): int
{
    $skillBin = __DIR__ . '/../vendor/bin/skill';
    
    if (!file_exists($skillBin)) {
        echo "Error: skill binary not found. Run composer install.\n";
        return 1;
    }
    
    $output = [];
    $returnCode = 0;
    exec($skillBin . ' portfolio:show 2>&1', $output, $returnCode);
    
    if ($returnCode !== 0) {
        echo "Error: failed to fetch portfolio\n";
        echo implode("\n", $output) . "\n";
        return 1;
    }
    
    $positions = [];
    foreach ($output as $line) {
        if (preg_match('/^\s*([A-Z]+)\s+/', $line, $m)) {
            $ticker = $m[1];
            if (!in_array($ticker, $positions) && $ticker !== 'TICKER') {
                $positions[] = $ticker;
            }
        }
    }
    
    if (empty($positions)) {
        $json = implode('', $output);
        $data = json_decode($json, true);
        if (isset($data['positions'])) {
            foreach ($data['positions'] as $pos) {
                $ticker = $pos['ticker'] ?? $pos['figi'] ?? null;
                if ($ticker && !in_array($ticker, $positions)) {
                    $positions[] = $ticker;
                }
            }
        }
    }
    
    if (empty($positions)) {
        echo "No positions found in portfolio.\n";
        return 0;
    }
    
    $userData = load();
    $userData['positions'] = $positions;
    $userData['positions_updated_at'] = date('c');
    save($userData);
    
    echo "Synced " . count($positions) . " positions: " . implode(', ', $positions) . "\n";
    
    return 0;
}

function show_positions(): int
{
    $data = load();
    $positions = $data['positions'] ?? [];
    
    if (empty($positions)) {
        echo "No positions synced. Run: php scripts/memory.php sync-portfolio\n";
        return 0;
    }
    
    echo "=== Portfolio Positions ===\n\n";
    foreach ($positions as $ticker) {
        echo "• {$ticker}\n";
    }
    
    if ($data['positions_updated_at'] ?? null) {
        echo "\nLast synced: {$data['positions_updated_at']}\n";
    }
    
    return 0;
}

function parse_args(array $args): array
{
    $result = [];
    
    foreach ($args as $arg) {
        if (str_starts_with($arg, '--')) {
            $parts = explode('=', substr($arg, 2), 2);
            $result[$parts[0]] = $parts[1] ?? true;
        }
    }
    
    return $result;
}

exit(main($argv));
