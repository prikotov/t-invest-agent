<?php

declare(strict_types=1);

namespace App\Console\Command\Skill;

use App\Service\Skill\Manager;
use App\Service\Skill\ProfileManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('skill:profile', 'Apply or list skill profiles')]
class ProfileCommand extends Command
{
    public function __construct(
        private readonly ProfileManager $profileManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('profile', InputArgument::OPTIONAL, 'Profile name to apply')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Target agent: opencode, kilocode, all', 'opencode')
            ->setDescription('Apply a skill profile or list available profiles');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $profile = $input->getArgument('profile');

        if (!$profile) {
            return $this->listProfiles($output);
        }

        $target = $input->getOption('target');
        $targets = $this->resolveTargets($target, $io);

        if ($targets === null) {
            return Command::FAILURE;
        }

        $hasErrors = false;

        foreach ($targets as $t) {
            $io->section(sprintf('Target: %s', $t));
            $result = $this->profileManager->applyProfile($profile, $t);

            if (isset($result['error'])) {
                $io->error($result['error']);
                $hasErrors = true;
                continue;
            }

            if (!empty($result['enabled'])) {
                $io->success('Enabled: ' . implode(', ', $result['enabled']));
            }

            if (!empty($result['disabled'])) {
                $io->note('Disabled: ' . implode(', ', $result['disabled']));
            }

            if (!empty($result['errors'])) {
                foreach ($result['errors'] as $error) {
                    $io->warning($error);
                }
            }
        }

        return $hasErrors ? Command::FAILURE : Command::SUCCESS;
    }

    private function listProfiles(OutputInterface $output): int
    {
        $profiles = $this->profileManager->getProfiles();

        if (empty($profiles)) {
            $output->writeln('No profiles configured in config/skills.yaml');
            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['Profile', 'Skills']);

        foreach ($profiles as $profile) {
            $skills = $this->profileManager->getProfileSkills($profile);
            $table->addRow([
                $profile,
                implode(', ', $skills ?? [])
            ]);
        }

        $table->render();
        $output->writeln("\nUsage: <info>skill:profile <profile_name> [--target=opencode|kilocode|all]</info>");

        return Command::SUCCESS;
    }

    private function resolveTargets(string $target, SymfonyStyle $io): ?array
    {
        if ($target === 'all') {
            return Manager::getValidTargets();
        }

        if (!Manager::isValidTarget($target)) {
            $io->error(sprintf(
                'Invalid target "%s". Valid targets: %s',
                $target,
                implode(', ', Manager::getValidTargets())
            ));
            return null;
        }

        return [$target];
    }
}
