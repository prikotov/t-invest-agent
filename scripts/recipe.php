#!/usr/bin/env php
<?php

declare(strict_types=1);

const DATA_DIR = __DIR__ . '/../data/recipes';

function main(array $argv): int
{
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0755, true);
    }
    
    $command = $argv[1] ?? 'list';
    
    return match ($command) {
        'create' => create(array_slice($argv, 2)),
        'list' => list_recipes(array_slice($argv, 2)),
        'show' => show($argv[2] ?? null),
        'update' => update(array_slice($argv, 2)),
        'delete' => delete($argv[2] ?? null),
        default => help(),
    };
}

function help(): int
{
    echo "Usage: recipe.php <command> [options]\n\n";
    echo "Commands:\n";
    echo "  create --ticker=TICKER --direction=LONG|SHORT --entry=PRICE --target=PRICE --stop=PRICE [--thesis=...]\n";
    echo "  list [--status=STATUS] [--ticker=TICKER]\n";
    echo "  show <id>\n";
    echo "  update <id> --status=STATUS [--note=...]\n";
    echo "  delete <id>\n";
    return 0;
}

function create(array $args): int
{
    $parsed = parse_args($args);
    
    $ticker = $parsed['ticker'] ?? null;
    $direction = $parsed['direction'] ?? null;
    $entry = isset($parsed['entry']) ? (float) $parsed['entry'] : null;
    $target = isset($parsed['target']) ? (float) $parsed['target'] : null;
    $stop = isset($parsed['stop']) ? (float) $parsed['stop'] : null;
    $thesis = $parsed['thesis'] ?? '';
    
    if (!$ticker || !$direction || !$entry || !$target || !$stop) {
        echo "Error: --ticker, --direction, --entry, --target, --stop required\n";
        return 1;
    }
    
    $direction = strtoupper($direction);
    if (!in_array($direction, ['LONG', 'SHORT'])) {
        echo "Error: --direction must be LONG or SHORT\n";
        return 1;
    }
    
    $date = date('Y-m-d');
    $count = count(glob(DATA_DIR . "/recipe-{$date}-*.json")) + 1;
    $id = "recipe-{$date}-" . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    
    $risk = abs($entry - $stop);
    $reward = abs($target - $entry);
    $rr = $risk > 0 ? round($reward / $risk, 2) : 0;
    
    $riskPct = round($risk / $entry * 100, 2);
    $rewardPct = round($reward / $entry * 100, 2);
    
    $recipe = [
        'id' => $id,
        'ticker' => strtoupper($ticker),
        'direction' => $direction,
        'entry' => $entry,
        'target' => $target,
        'stop' => $stop,
        'rr' => $rr,
        'risk_pct' => $riskPct,
        'reward_pct' => $rewardPct,
        'thesis' => $thesis,
        'status' => 'ACTIVE',
        'note' => null,
        'created_at' => date('c'),
        'updated_at' => date('c'),
    ];
    
    file_put_contents(DATA_DIR . "/{$id}.json", json_encode($recipe, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "Created: {$id}\n";
    echo "  Ticker: {$ticker}\n";
    echo "  Direction: {$direction}\n";
    echo "  Entry: {$entry}\n";
    echo "  Target: {$target} (+{$rewardPct}%)\n";
    echo "  Stop: {$stop} (-{$riskPct}%)\n";
    echo "  R:R: 1:{$rr}\n";
    
    return 0;
}

function list_recipes(array $args): int
{
    $parsed = parse_args($args);
    $filterStatus = $parsed['status'] ?? null;
    $filterTicker = $parsed['ticker'] ?? null;
    
    $files = glob(DATA_DIR . '/*.json');
    
    if (empty($files)) {
        echo "No recipes found.\n";
        return 0;
    }
    
    usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
    
    foreach ($files as $file) {
        $recipe = json_decode(file_get_contents($file), true);
        
        if ($filterStatus && $recipe['status'] !== $filterStatus) {
            continue;
        }
        
        if ($filterTicker && $recipe['ticker'] !== strtoupper($filterTicker)) {
            continue;
        }
        
        echo "\n{$recipe['id']} [{$recipe['status']}]\n";
        echo "  {$recipe['direction']} {$recipe['ticker']}\n";
        echo "  Entry: {$recipe['entry']} → Target: {$recipe['target']} → Stop: {$recipe['stop']}\n";
        echo "  R:R: 1:{$recipe['rr']} | Risk: {$recipe['risk_pct']}% | Reward: +{$recipe['reward_pct']}%\n";
        if ($recipe['thesis']) {
            echo "  Thesis: {$recipe['thesis']}\n";
        }
    }
    
    return 0;
}

function show(?string $id): int
{
    if (!$id) {
        echo "Error: recipe id required\n";
        return 1;
    }
    
    $file = DATA_DIR . "/{$id}.json";
    
    if (!file_exists($file)) {
        echo "Error: recipe not found\n";
        return 1;
    }
    
    $recipe = json_decode(file_get_contents($file), true);
    
    echo json_encode($recipe, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
    return 0;
}

function update(array $args): int
{
    if (count($args) < 2) {
        echo "Error: recipe id and options required\n";
        return 1;
    }
    
    $id = $args[0];
    $parsed = parse_args(array_slice($args, 1));
    
    $file = DATA_DIR . "/{$id}.json";
    
    if (!file_exists($file)) {
        echo "Error: recipe not found\n";
        return 1;
    }
    
    $recipe = json_decode(file_get_contents($file), true);
    
    if (isset($parsed['status'])) {
        $recipe['status'] = strtoupper($parsed['status']);
    }
    
    if (isset($parsed['note'])) {
        $recipe['note'] = $parsed['note'];
    }
    
    $recipe['updated_at'] = date('c');
    
    file_put_contents($file, json_encode($recipe, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo "Updated: {$id}\n";
    echo "  Status: {$recipe['status']}\n";
    
    return 0;
}

function delete(?string $id): int
{
    if (!$id) {
        echo "Error: recipe id required\n";
        return 1;
    }
    
    $file = DATA_DIR . "/{$id}.json";
    
    if (!file_exists($file)) {
        echo "Error: recipe not found\n";
        return 1;
    }
    
    unlink($file);
    echo "Deleted: {$id}\n";
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
