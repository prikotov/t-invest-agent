<?php

declare(strict_types=1);

namespace App\Service\Skill;

use Symfony\Component\Yaml\Yaml;

final class ProfileManager
{
    private ?array $config = null;

    public function __construct(
        private readonly string $projectDir,
        private readonly Manager $manager
    ) {
    }

    public function getProfiles(): array
    {
        $this->loadConfig();
        return array_keys($this->config['skills'] ?? []);
    }

    public function getProfileSkills(string $profile): ?array
    {
        $this->loadConfig();
        return $this->config['skills'][$profile] ?? null;
    }

    public function applyProfile(string $profile, string $target = Manager::TARGET_OPENCODE): array
    {
        $skills = $this->getProfileSkills($profile);
        if ($skills === null) {
            return ['error' => "Profile '{$profile}' not found"];
        }

        $this->manager->cleanupBrokenSymlinks($target);

        $results = ['enabled' => [], 'disabled' => [], 'errors' => []];
        $available = array_keys($this->manager->getAvailableSkills());

        foreach ($skills as $skillName) {
            if (!in_array($skillName, $available, true)) {
                $results['errors'][] = "Skill '{$skillName}' not available";
                continue;
            }
            if ($this->manager->enable($skillName, $target)) {
                $results['enabled'][] = $skillName;
            }
        }

        $enabled = array_keys($this->manager->getManagedEnabledSkills($target));
        foreach ($enabled as $skillName) {
            if (!in_array($skillName, $skills, true)) {
                $this->manager->disable($skillName, $target);
                $results['disabled'][] = $skillName;
            }
        }

        return $results;
    }

    private function loadConfig(): void
    {
        if ($this->config !== null) {
            return;
        }

        $configFile = $this->projectDir . '/config/skills.yaml';
        if (!file_exists($configFile)) {
            $this->config = ['skills' => []];
            return;
        }

        $this->config = Yaml::parseFile($configFile);
    }
}
