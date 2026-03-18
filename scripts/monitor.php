#!/usr/bin/env php
<?php

declare(strict_types=1);

const DATA_DIR = __DIR__ . '/../data/monitors';

function main(array $argv): int
{
    $command = $argv[1] ?? 'list';
    
    return match ($command) {
        'create' => create(array_slice($argv, 2)),
        'list' => list_monitors(array_slice($argv, 2)),
        'check' => check_all(),
        'run' => run($argv[2] ?? null),
        'delete' => delete($argv[2] ?? null),
        default => help(),
    };
}

function help(): int
{
    echo "Usage: monitor.php <command> [options]\n\n";
    echo "Commands:\n";
    echo "  create price --ticker=TICKER --level=PRICE --direction=UP|DOWN [--recipe=ID]\n";
    echo "  create schedule --cron=EXPR --prompt=PROMPT\n";
    echo "  list [--type=price|schedule]\n";
    echo "  check\n";
    echo "  run <id>\n";
    echo "  delete <id>\n";
    return 0;
}

function create(array $args): int
{
    $type = $args[0] ?? null;
    
    if ($type === 'price') {
        return create_price_monitor($args);
    } elseif ($type === 'schedule') {
        return create_schedule_monitor($args);
    }
    
    echo "Error: type must be 'price' or 'schedule'\n";
    return 1;
}

function create_price_monitor(array $args): int
{
    $parsed = parse_args($args);
    
    $ticker = $parsed['ticker'] ?? null;
    $level = isset($parsed['level']) ? (float) $parsed['level'] : null;
    $direction = $parsed['direction'] ?? null;
    $recipeId = $parsed['recipe'] ?? null;
    
    if (!$ticker || !$level || !$direction) {
        echo "Error: --ticker, --level, --direction required\n";
        return 1;
    }
    
    $direction = strtoupper($direction);
    if (!in_array($direction, ['UP', 'DOWN'])) {
        echo "Error: --direction must be UP or DOWN\n";
        return 1;
    }
    
    $id = 'monitor-' . str_pad((string) (count(glob(DATA_DIR . '/*.json')) + 1), 3, '0', STR_PAD_LEFT);
    
    $monitor = [
        'id' => $id,
        'type' => 'price',
        'ticker' => $ticker,
        'level' => $level,
        'direction' => $direction,
        'recipe_id' => $recipeId,
        'action' => null,
        'status' => 'ACTIVE',
        'created_at' => date('c'),
        'triggered_at' => null,
    ];
    
    file_put_contents(DATA_DIR . "/{$id}.json", json_encode($monitor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "Created: {$id}\n";
    return 0;
}

function create_schedule_monitor(array $args): int
{
    $parsed = parse_args($args);
    
    $cron = $parsed['cron'] ?? null;
    $prompt = $parsed['prompt'] ?? null;
    
    if (!$cron || !$prompt) {
        echo "Error: --cron, --prompt required\n";
        return 1;
    }
    
    $id = 'monitor-' . str_pad((string) (count(glob(DATA_DIR . '/*.json')) + 1), 3, '0', STR_PAD_LEFT);
    
    $monitor = [
        'id' => $id,
        'type' => 'schedule',
        'cron' => $cron,
        'prompt' => $prompt,
        'status' => 'ACTIVE',
        'last_run' => null,
        'next_run' => null,
    ];
    
    file_put_contents(DATA_DIR . "/{$id}.json", json_encode($monitor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "Created: {$id}\n";
    return 0;
}

function list_monitors(array $args): int
{
    $parsed = parse_args($args);
    $filterType = $parsed['type'] ?? null;
    
    $files = glob(DATA_DIR . '/*.json');
    
    if (empty($files)) {
        echo "No monitors found.\n";
        return 0;
    }
    
    foreach ($files as $file) {
        $monitor = json_decode(file_get_contents($file), true);
        
        if ($filterType && $monitor['type'] !== $filterType) {
            continue;
        }
        
        echo "\n{$monitor['id']} [{$monitor['status']}]\n";
        
        if ($monitor['type'] === 'price') {
            echo "  Type: price\n";
            echo "  Ticker: {$monitor['ticker']}\n";
            echo "  Level: {$monitor['level']}\n";
            echo "  Direction: {$monitor['direction']}\n";
            if ($monitor['recipe_id']) {
                echo "  Recipe: {$monitor['recipe_id']}\n";
            }
        } else {
            echo "  Type: schedule\n";
            echo "  Cron: {$monitor['cron']}\n";
            echo "  Prompt: {$monitor['prompt']}\n";
        }
    }
    
    return 0;
}

function check_all(): int
{
    $files = glob(DATA_DIR . '/*.json');
    $triggered = [];
    
    foreach ($files as $file) {
        $monitor = json_decode(file_get_contents($file), true);
        
        if ($monitor['type'] !== 'price' || $monitor['status'] !== 'ACTIVE') {
            continue;
        }
        
        $price = get_price($monitor['ticker']);
        
        if ($price === null) {
            echo "Warning: Could not get price for {$monitor['ticker']}\n";
            continue;
        }
        
        $isTriggered = false;
        
        if ($monitor['direction'] === 'UP' && $price >= $monitor['level']) {
            $isTriggered = true;
        } elseif ($monitor['direction'] === 'DOWN' && $price <= $monitor['level']) {
            $isTriggered = true;
        }
        
        if ($isTriggered) {
            $monitor['status'] = 'TRIGGERED';
            $monitor['triggered_at'] = date('c');
            file_put_contents($file, json_encode($monitor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            $triggered[] = [
                'id' => $monitor['id'],
                'ticker' => $monitor['ticker'],
                'price' => $price,
                'level' => $monitor['level'],
                'direction' => $monitor['direction'],
            ];
        }
    }
    
    if (empty($triggered)) {
        echo "No alerts triggered.\n";
        return 0;
    }
    
    echo "TRIGGERED:\n";
    foreach ($triggered as $t) {
        echo "  {$t['id']}: {$t['ticker']} @ {$t['price']} (target: {$t['direction']} {$t['level']})\n";
    }
    
    return 0;
}

function run(?string $id): int
{
    if (!$id) {
        echo "Error: monitor id required\n";
        return 1;
    }
    
    $file = DATA_DIR . "/{$id}.json";
    
    if (!file_exists($file)) {
        echo "Error: monitor not found\n";
        return 1;
    }
    
    $monitor = json_decode(file_get_contents($file), true);
    
    if ($monitor['type'] === 'schedule') {
        $promptPath = resolve_prompt_path($monitor['prompt']);
        
        if (!$promptPath || !file_exists($promptPath)) {
            echo "Error: prompt file not found: {$monitor['prompt']}\n";
            return 1;
        }
        
        $monitor['last_run'] = date('c');
        file_put_contents($file, json_encode($monitor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        echo file_get_contents($promptPath) . "\n";
        return 0;
    }
    
    echo json_encode($monitor, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    return 0;
}

function resolve_prompt_path(string $prompt): ?string
{
    if (str_starts_with($prompt, '@')) {
        return __DIR__ . '/../prompts/' . substr($prompt, 1) . '.md';
    }
    
    if (str_starts_with($prompt, '/')) {
        return $prompt;
    }
    
    return __DIR__ . '/../' . $prompt;
}

function delete(?string $id): int
{
    if (!$id) {
        echo "Error: monitor id required\n";
        return 1;
    }
    
    $file = DATA_DIR . "/{$id}.json";
    
    if (!file_exists($file)) {
        echo "Error: monitor not found\n";
        return 1;
    }
    
    unlink($file);
    echo "Deleted: {$id}\n";
    return 0;
}

function get_price(string $ticker): ?float
{
    $bin = __DIR__ . '/../vendor/bin/moex';
    $output = [];
    exec("{$bin} security:trade-data " . escapeshellarg($ticker) . " 2>&1", $output, $returnCode);
    
    if ($returnCode !== 0) {
        return null;
    }
    
    foreach ($output as $line) {
        if (preg_match('/Last:\s+([\d.]+)/', $line, $matches)) {
            return (float) $matches[1];
        }
    }
    
    return null;
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
