<?php

declare(strict_types=1);

namespace App\Console\Command\Skill;

use App\Service\Skill\Manager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'skill:list', description: 'List available and enabled skills')]
class ListCommand extends Command
{
    public function __construct(
        private readonly Manager $manager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $skills = $this->manager->getAvailableSkills();
        $enabled = $this->manager->getEnabledSkills();

        if (empty($skills)) {
            $io->comment('No skills found in skills/ directory.');
            return Command::SUCCESS;
        }

        $io->section('Skills');

        $rows = [];
        foreach ($skills as $name => $skill) {
            $status = isset($enabled[$name]) ? '<info>✓ enabled</info>' : '<comment>○ disabled</comment>';
            $rows[] = [$name, $status, $skill['description']];
        }

        $io->table(['Skill', 'Status', 'Description'], $rows);

        $io->writeln('Run <comment>agent skill:manage</comment> to enable/disable skills.');

        return Command::SUCCESS;
    }
}
