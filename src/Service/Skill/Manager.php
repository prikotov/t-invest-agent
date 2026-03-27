<?php

declare(strict_types=1);

namespace App\Service\Skill;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class Manager
{
    public const TARGET_OPENCODE = 'opencode';
    public const TARGET_KILOCODE = 'kilocode';

    private const TARGET_DIRS = [
        self::TARGET_OPENCODE => '.agents/skills',
        self::TARGET_KILOCODE => '.kilocode/skills',
    ];

    private string $skillsSourceDir;
    private Filesystem $fs;

    public function __construct(
        private readonly string $projectDir
    ) {
        $this->skillsSourceDir = $projectDir . '/skills';
        $this->fs = new Filesystem();
    }

    public function getAvailableSkills(): array
    {
        if (!is_dir($this->skillsSourceDir)) {
            return [];
        }

        $skills = [];
        foreach (glob($this->skillsSourceDir . '/*/SKILL.md') as $skillFile) {
            $name = basename(dirname($skillFile));
            $skills[$name] = [
                'name' => $name,
                'path' => dirname($skillFile),
                'description' => $this->parseDescription($skillFile),
            ];
        }

        ksort($skills);
        return $skills;
    }

    public function getEnabledSkills(string $target = self::TARGET_OPENCODE): array
    {
        $targetDir = $this->getTargetDir($target);
        if (!is_dir($targetDir)) {
            return [];
        }

        $enabled = [];
        foreach (scandir($targetDir) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $link = $targetDir . '/' . $entry;
            if (is_link($link)) {
                $enabled[basename($link)] = true;
            }
        }

        return $enabled;
    }

    public function cleanupBrokenSymlinks(string $target = self::TARGET_OPENCODE): int
    {
        $targetDir = $this->getTargetDir($target);
        if (!is_dir($targetDir)) {
            return 0;
        }

        $removed = 0;
        foreach (scandir($targetDir) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $link = $targetDir . '/' . $entry;
            if (is_link($link) && !file_exists($link)) {
                $this->fs->remove($link);
                $removed++;
            }
        }

        return $removed;
    }

    public function isManagedSymlink(string $link): bool
    {
        if (!is_link($link)) {
            return false;
        }

        $target = readlink($link);
        if ($target === false) {
            return false;
        }

        $realTarget = realpath(dirname($link) . '/' . $target);
        if ($realTarget === false) {
            return str_starts_with($target, '../../skills/') || str_starts_with($target, '../skills/');
        }

        return str_starts_with($realTarget, $this->skillsSourceDir);
    }

    public function getManagedEnabledSkills(string $target = self::TARGET_OPENCODE): array
    {
        $targetDir = $this->getTargetDir($target);
        if (!is_dir($targetDir)) {
            return [];
        }

        $enabled = [];
        foreach (scandir($targetDir) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $link = $targetDir . '/' . $entry;
            if (is_link($link) && $this->isManagedSymlink($link)) {
                $enabled[basename($link)] = true;
            }
        }

        return $enabled;
    }

    public function enable(string $skillName, string $target = self::TARGET_OPENCODE): bool
    {
        $skills = $this->getAvailableSkills();
        if (!isset($skills[$skillName])) {
            return false;
        }

        $targetDir = $this->getTargetDir($target);
        $this->fs->mkdir($targetDir);

        $source = $skills[$skillName]['path'];
        $link = $targetDir . '/' . $skillName;

        if ($this->fs->exists($link)) {
            $this->fs->remove($link);
        }

        $this->fs->symlink(
            Path::makeRelative($source, dirname($link)),
            $link
        );

        return true;
    }

    public function disable(string $skillName, string $target = self::TARGET_OPENCODE): bool
    {
        $link = $this->getTargetDir($target) . '/' . $skillName;

        if (!$this->fs->exists($link)) {
            return false;
        }

        $this->fs->remove($link);
        return true;
    }

    public static function getValidTargets(): array
    {
        return [self::TARGET_OPENCODE, self::TARGET_KILOCODE];
    }

    public static function isValidTarget(string $target): bool
    {
        return isset(self::TARGET_DIRS[$target]);
    }

    private function getTargetDir(string $target): string
    {
        if (!isset(self::TARGET_DIRS[$target])) {
            throw new \InvalidArgumentException(
                sprintf('Invalid target "%s". Valid targets: %s', $target, implode(', ', self::getValidTargets()))
            );
        }

        return $this->projectDir . '/' . self::TARGET_DIRS[$target];
    }

    private function parseDescription(string $skillFile): string
    {
        $content = file_get_contents($skillFile);
        if (preg_match('/^---\s*\n(.*?)\n---\s*\n/s', $content, $matches)) {
            if (preg_match('/description:\s*(.+)/m', $matches[1], $desc)) {
                return trim($desc[1]);
            }
        }
        return '';
    }
}
