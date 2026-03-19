<?php

declare(strict_types=1);

namespace Tests\Skills;

use PHPUnit\Framework\TestCase;

class MonitorSkillTest extends TestCase
{
    private string $script;
    private string $dataDir;

    protected function setUp(): void
    {
        $this->script = __DIR__ . '/../../skills/monitor/scripts/monitor.php';
        $this->dataDir = __DIR__ . '/../../data/monitors';
    }

    public function testListEmptyWithFilter(): void
    {
        $output = $this->runScript('list --type=nonexistent');
        $this->assertStringContainsString('No monitors found', $output);
    }

    public function testCreatePriceMonitor(): void
    {
        $output = $this->runScript('create price --ticker=SBER --level=260 --direction=UP');

        $this->assertStringContainsString('Monitor created:', $output);
        $this->assertStringContainsString('SBER', $output);
        $this->assertStringContainsString('price', $output);
    }

    public function testCreatePriceMonitorWithRecipe(): void
    {
        $output = $this->runScript('create price --ticker=GAZP --level=180 --direction=DOWN --recipe=recipe-001');

        $this->assertStringContainsString('Monitor created:', $output);
        $this->assertStringContainsString('recipe-001', $output);
    }

    public function testCreateScheduleMonitor(): void
    {
        $output = $this->runScript('create schedule --cron="0 9 * * 1-5" --prompt="@morning"');

        $this->assertStringContainsString('Monitor created:', $output);
        $this->assertStringContainsString('schedule', $output);
        $this->assertStringContainsString('@morning', $output);
    }

    public function testInvalidType(): void
    {
        $output = $this->runScript('create invalid', true);
        $this->assertStringContainsString('price|schedule', $output);
    }

    public function testMissingPriceParams(): void
    {
        $output = $this->runScript('create price --ticker=SBER', true);
        $this->assertStringContainsString('--ticker and --level required', $output);
    }

    public function testCheckNoMonitors(): void
    {
        $output = $this->runScript('check');
        $this->assertStringContainsString('No monitors', $output);
    }

    private function runScript(string $args, bool $expectFailure = false): string
    {
        $cmd = sprintf('php %s %s 2>&1', escapeshellarg($this->script), $args);
        $output = shell_exec($cmd);

        return $output ?: '';
    }
}
