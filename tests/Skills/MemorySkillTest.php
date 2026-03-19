<?php

declare(strict_types=1);

namespace Tests\Skills;

use PHPUnit\Framework\TestCase;

class MemorySkillTest extends TestCase
{
    private string $script;
    private string $dataFile;

    protected function setUp(): void
    {
        $this->script = __DIR__ . '/../../skills/memory/scripts/memory.php';
        $this->dataFile = __DIR__ . '/../../data/memory/user.json';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dataFile)) {
            unlink($this->dataFile);
        }
    }

    public function testProfileShowsDefaults(): void
    {
        $output = $this->runScript('profile');

        $this->assertStringContainsString('User Profile', $output);
        $this->assertStringContainsString('moderate', $output);
        $this->assertStringContainsString('long-term', $output);
        $this->assertStringContainsString('value', $output);
    }

    public function testSetSingleValue(): void
    {
        $output = $this->runScript('set risk_tolerance aggressive');

        $this->assertStringContainsString('Set: risk_tolerance = aggressive', $output);

        $output = $this->runScript('get risk_tolerance');
        $this->assertStringContainsString('aggressive', $output);
    }

    public function testSetNumericValue(): void
    {
        $this->runScript('set max_position_pct 15');
        $output = $this->runScript('get max_position_pct');

        $this->assertStringContainsString('15', $output);
    }

    public function testUpdateProfile(): void
    {
        $output = $this->runScript('update --risk=aggressive --horizon=short-term');

        $this->assertStringContainsString('Profile updated:', $output);
        $this->assertStringContainsString('risk_tolerance = aggressive', $output);
        $this->assertStringContainsString('horizon = short-term', $output);
    }

    public function testGetInvalidKey(): void
    {
        $this->runScript('set test value');
        $output = $this->runScript('get invalid_key', true);
        $this->assertStringContainsString('Key not found', $output);
    }

    public function testClearMemory(): void
    {
        $this->runScript('set risk_tolerance aggressive');
        $this->assertFileExists($this->dataFile);

        $output = $this->runScript('clear');
        $this->assertStringContainsString('Memory cleared', $output);
        $this->assertFileDoesNotExist($this->dataFile);
    }

    private function runScript(string $args, bool $expectFailure = false): string
    {
        $cmd = sprintf('php %s %s 2>&1', escapeshellarg($this->script), $args);
        $output = shell_exec($cmd);

        return $output ?: '';
    }
}
