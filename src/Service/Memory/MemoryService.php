<?php

declare(strict_types=1);

namespace App\Service\Memory;

use Memory\Core\Storage\StorageInterface;

final class MemoryService
{
    private const DEFAULT_PROFILE = [
        'risk_tolerance' => 'moderate',
        'horizon' => 'long-term',
        'style' => 'value',
        'favorite_sectors' => [],
        'avoid_sectors' => [],
        'max_position_pct' => 10,
    ];

    private const ARG_MAPPINGS = [
        'risk' => 'risk_tolerance',
        'horizon' => 'horizon',
        'style' => 'style',
        'sectors' => 'favorite_sectors',
        'max-pos' => 'max_position_pct',
    ];

    private const VALUE_DESCRIPTIONS = [
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

    public function __construct(
        private readonly StorageInterface $storage
    ) {
    }

    public function get(string $key): mixed
    {
        if (!$this->storage->has($key)) {
            return null;
        }
        return $this->storage->get($key);
    }

    public function set(string $key, mixed $value): void
    {
        $value = $this->parseValue($value);
        $this->storage->set($key, $value);
    }

    public function update(array $args): array
    {
        $updated = [];

        foreach (self::ARG_MAPPINGS as $arg => $field) {
            if (!isset($args[$arg])) {
                continue;
            }

            $value = $args[$arg];
            if ($field === 'favorite_sectors') {
                $value = array_map('trim', explode(',', $value));
            } elseif ($field === 'max_position_pct') {
                $value = (int) $value;
            }

            $this->storage->set($field, $value);
            $updated[] = sprintf(
                '%s = %s',
                $field,
                is_array($value) ? json_encode($value) : (string) $value
            );
        }

        return $updated;
    }

    public function getProfile(): array
    {
        $stored = $this->storage->all();
        return array_merge(self::DEFAULT_PROFILE, $stored);
    }

    public function isCustomized(): bool
    {
        return $this->storage->exists();
    }

    public function clear(): void
    {
        $this->storage->clear();
    }

    public function getDescription(mixed $value): ?string
    {
        return self::VALUE_DESCRIPTIONS[$value] ?? null;
    }

    public function getAvailableKeys(): array
    {
        return array_keys($this->getProfile());
    }

    public function getDefaultProfile(): array
    {
        return self::DEFAULT_PROFILE;
    }

    private function parseValue(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        if (in_array($value, ['true', 'false'], true)) {
            return $value === 'true';
        }

        if (is_numeric($value)) {
            return str_contains($value, '.') ? (float) $value : (int) $value;
        }

        if (str_starts_with($value, '[') || str_starts_with($value, '{')) {
            $decoded = json_decode($value, true);
            if ($decoded !== null) {
                return $decoded;
            }
        }

        return $value;
    }
}
