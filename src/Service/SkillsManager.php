<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class SkillsManager
{
    private string $skillsSourceDir;
    private string $skillsTargetDir;
    private Filesystem $fs;

    public function __construct(
        private readonly string $projectDir
    ) {
        $this->skillsSourceDir = $projectDir . '/skills';
        $this->skillsTargetDir = $projectDir . '/.agents/skills';
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

    public function getEnabledSkills(): array
    {
        if (!is_dir($this->skillsTargetDir)) {
            return [];
        }

        $enabled = [];
        foreach (glob($this->skillsTargetDir . '/*') as $link) {
            if (is_link($link)) {
                $enabled[basename($link)] = true;
            }
        }

        return $enabled;
    }

    public function enable(string $skillName): bool
    {
        $skills = $this->getAvailableSkills();
        if (!isset($skills[$skillName])) {
            return false;
        }

        $this->fs->mkdir($this->skillsTargetDir);

        $source = $skills[$skillName]['path'];
        $target = $this->skillsTargetDir . '/' . $skillName;

        if ($this->fs->exists($target)) {
            $this->fs->remove($target);
        }

        $this->fs->symlink(
            Path::makeRelative($source, dirname($target)),
            $target
        );

        return true;
    }

    public function disable(string $skillName): bool
    {
        $target = $this->skillsTargetDir . '/' . $skillName;

        if (!$this->fs->exists($target)) {
            return false;
        }

        $this->fs->remove($target);
        return true;
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
