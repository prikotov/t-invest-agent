<?php

declare(strict_types=1);

namespace Tests\Skills;

use PHPUnit\Framework\TestCase;

class RecipeSkillTest extends TestCase
{
    private string $script;
    private string $dataDir;

    protected function setUp(): void
    {
        $this->script = __DIR__ . '/../../skills/recipe/scripts/recipe.php';
        $this->dataDir = __DIR__ . '/../../data/recipes';
    }

    public function testListEmptyWithFilter(): void
    {
        $output = $this->runScript('list --status=NONEXISTENT');
        $this->assertStringContainsString('No recipes found', $output);
    }

    public function testCreateRecipe(): void
    {
        $output = $this->runScript('create --ticker=TEST --direction=LONG --entry=100 --target=120 --stop=95');
        
        $this->assertStringContainsString('Recipe created:', $output);
        $this->assertStringContainsString('TEST', $output);
        $this->assertStringContainsString('LONG', $output);
        $this->assertStringContainsString('R:R', $output);
        $this->assertStringContainsString('Не является инвестиционной рекомендацией', $output);
    }

    public function testCreateWithThesis(): void
    {
        $output = $this->runScript('create --ticker=TEST2 --direction=SHORT --entry=200 --target=180 --stop=210 --thesis="Test thesis"');
        
        $this->assertStringContainsString('Recipe created:', $output);
        $this->assertStringContainsString('Test thesis', $output);
    }

    public function testListShowsCreated(): void
    {
        $this->runScript('create --ticker=TEST3 --direction=LONG --entry=100 --target=120 --stop=95');
        $output = $this->runScript('list');
        
        $this->assertStringContainsString('TEST3', $output);
    }

    public function testInvalidDirection(): void
    {
        $output = $this->runScript('create --ticker=TEST --direction=INVALID --entry=100 --target=120 --stop=95', true);
        $this->assertStringContainsString('LONG or SHORT', $output);
    }

    public function testMissingRequiredParams(): void
    {
        $output = $this->runScript('create --ticker=TEST', true);
        $this->assertStringContainsString('Usage:', $output);
    }

    private function runScript(string $args, bool $expectFailure = false): string
    {
        $cmd = sprintf('php %s %s 2>&1', escapeshellarg($this->script), $args);
        $output = shell_exec($cmd);
        
        if ($expectFailure) {
            return $output ?: '';
        }
        
        return $output ?: '';
    }
}
