#!/usr/bin/env php
<?php

$dataDir = __DIR__ . '/../../../data/recipes';
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
    'create' => createRecipe($parsedArgs, $dataDir),
    'list' => listRecipes($parsedArgs, $dataDir),
    'show' => showRecipe($parsedArgs, $dataDir),
    'update' => updateRecipe($parsedArgs, $dataDir),
    'delete' => deleteRecipe($parsedArgs, $dataDir),
    default => listRecipes($parsedArgs, $dataDir),
};

function createRecipe(array $args, string $dataDir): void
{
    $ticker = $args['ticker'] ?? null;
    $direction = strtoupper($args['direction'] ?? 'LONG');
    $entry = isset($args['entry']) ? (float)$args['entry'] : null;
    $target = isset($args['target']) ? (float)$args['target'] : null;
    $stop = isset($args['stop']) ? (float)$args['stop'] : null;
    $thesis = $args['thesis'] ?? '';

    if (!$ticker || !$entry || !$target || !$stop) {
        echo "Usage: php recipe.php create --ticker=TICKER --direction=LONG|SHORT --entry=PRICE --target=PRICE --stop=PRICE [--thesis=...]\n";
        exit(1);
    }

    if (!in_array($direction, ['LONG', 'SHORT'])) {
        echo "Error: direction must be LONG or SHORT\n";
        exit(1);
    }

    $riskPct = abs($entry - $stop) / $entry * 100;
    $rewardPct = abs($target - $entry) / $entry * 100;
    $rr = $rewardPct / $riskPct;

    $id = 'recipe-' . date('Y-m-d') . '-' . str_pad(count(glob($dataDir . '/*.json')) + 1, 3, '0', STR_PAD_LEFT);

    $recipe = [
        'id' => $id,
        'ticker' => strtoupper($ticker),
        'direction' => $direction,
        'entry' => $entry,
        'target' => $target,
        'stop' => $stop,
        'risk_pct' => round($riskPct, 2),
        'reward_pct' => round($rewardPct, 2),
        'rr' => round($rr, 2),
        'thesis' => $thesis,
        'status' => 'ACTIVE',
        'created_at' => date('c'),
    ];

    file_put_contents($dataDir . '/' . $id . '.json', json_encode($recipe, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "Recipe created: $id\n\n";
    echo "| Field    | Value |\n";
    echo "|----------|-------|\n";
    echo "| Ticker   | {$recipe['ticker']} |\n";
    echo "| Direction| {$recipe['direction']} |\n";
    echo "| Entry    | {$recipe['entry']} |\n";
    echo "| Target   | {$recipe['target']} |\n";
    echo "| Stop     | {$recipe['stop']} |\n";
    echo "| R:R      | 1:{$recipe['rr']} |\n";
    echo "| Risk     | {$recipe['risk_pct']}% |\n";
    echo "| Reward   | {$recipe['reward_pct']}% |\n";

    if ($thesis) {
        echo "\n**Thesis:** $thesis\n";
    }

    echo "\nНе является инвестиционной рекомендацией.\n";
    echo "{$recipe['ticker']}\n";
}

function listRecipes(array $args, string $dataDir): void
{
    $statusFilter = $args['status'] ?? null;

    $files = glob($dataDir . '/*.json');
    if (empty($files)) {
        echo "No recipes found.\n";
        return;
    }

    $recipes = [];
    foreach ($files as $file) {
        $recipe = json_decode(file_get_contents($file), true);
        if ($statusFilter && $recipe['status'] !== strtoupper($statusFilter)) {
            continue;
        }
        $recipes[] = $recipe;
    }

    if (empty($recipes)) {
        echo "No recipes found";
        if ($statusFilter) echo " with status: $statusFilter";
        echo ".\n";
        return;
    }

    usort($recipes, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

    echo "| ID | Ticker | Dir | Entry | Target | Stop | R:R | Status |\n";
    echo "|----|--------|-----|-------|--------|------|-----|--------|\n";

    foreach ($recipes as $r) {
        printf(
            "| %s | %s | %s | %s | %s | %s | 1:%.1f | %s |\n",
            $r['id'],
            $r['ticker'],
            $r['direction'],
            $r['entry'],
            $r['target'],
            $r['stop'],
            $r['rr'],
            $r['status']
        );
    }

    echo "\nTotal: " . count($recipes) . " recipes\n";
    echo "\nНе является инвестиционной рекомендацией.\n";
}

function showRecipe(array $args, string $dataDir): void
{
    $id = $args['positional'][0] ?? null;

    if (!$id) {
        echo "Usage: php recipe.php show <id>\n";
        exit(1);
    }

    $file = $dataDir . '/' . $id . '.json';
    if (!file_exists($file)) {
        echo "Recipe not found: $id\n";
        exit(1);
    }

    $recipe = json_decode(file_get_contents($file), true);

    echo "# Recipe: {$recipe['id']}\n\n";
    echo "| Field     | Value |\n";
    echo "|-----------|-------|\n";
    echo "| Ticker    | {$recipe['ticker']} |\n";
    echo "| Direction | {$recipe['direction']} |\n";
    echo "| Entry     | {$recipe['entry']} |\n";
    echo "| Target    | {$recipe['target']} |\n";
    echo "| Stop      | {$recipe['stop']} |\n";
    echo "| R:R       | 1:{$recipe['rr']} |\n";
    echo "| Risk      | {$recipe['risk_pct']}% |\n";
    echo "| Reward    | {$recipe['reward_pct']}% |\n";
    echo "| Status    | {$recipe['status']} |\n";
    echo "| Created   | {$recipe['created_at']} |\n";

    if (!empty($recipe['thesis'])) {
        echo "\n**Thesis:**\n{$recipe['thesis']}\n";
    }

    echo "\nНе является инвестиционной рекомендацией.\n";
    echo "{$recipe['ticker']}\n";
}

function updateRecipe(array $args, string $dataDir): void
{
    $id = $args['positional'][0] ?? null;
    $status = $args['status'] ?? null;

    if (!$id) {
        echo "Usage: php recipe.php update <id> --status=STATUS [--note=...]\n";
        exit(1);
    }

    $file = $dataDir . '/' . $id . '.json';
    if (!file_exists($file)) {
        echo "Recipe not found: $id\n";
        exit(1);
    }

    $recipe = json_decode(file_get_contents($file), true);

    if ($status) {
        $status = strtoupper($status);
        if (!in_array($status, ['ACTIVE', 'TRIGGERED', 'CANCELLED', 'DONE'])) {
            echo "Invalid status. Use: ACTIVE, TRIGGERED, CANCELLED, DONE\n";
            exit(1);
        }
        $recipe['status'] = $status;
    }

    if (isset($args['note'])) {
        $recipe['notes'] = $recipe['notes'] ?? [];
        $recipe['notes'][] = ['date' => date('c'), 'text' => $args['note']];
    }

    $recipe['updated_at'] = date('c');

    file_put_contents($file, json_encode($recipe, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    echo "Recipe updated: $id\n";
    echo "Status: {$recipe['status']}\n";

    echo "\nНе является инвестиционной рекомендацией.\n";
    echo "{$recipe['ticker']}\n";
}

function deleteRecipe(array $args, string $dataDir): void
{
    $id = $args['positional'][0] ?? null;

    if (!$id) {
        echo "Usage: php recipe.php delete <id>\n";
        exit(1);
    }

    $file = $dataDir . '/' . $id . '.json';
    if (!file_exists($file)) {
        echo "Recipe not found: $id\n";
        exit(1);
    }

    $recipe = json_decode(file_get_contents($file), true);
    unlink($file);

    echo "Recipe deleted: $id\n";
    echo "\nНе является инвестиционной рекомендацией.\n";
    echo "{$recipe['ticker']}\n";
}
