<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Service\SkillsManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'skills:enable', description: 'Enable specific skill(s)')]
class SkillsEnableCommand extends Command
{
    public function __construct(
        private readonly SkillsManager $manager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('skills', InputArgument::IS_ARRAY, 'Skill names to enable');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $skills = $input->getArgument('skills');

        if (empty($skills)) {
            $io->error('Specify skill name(s): agent skills:enable <skill>');
            return Command::FAILURE;
        }

        foreach ($skills as $name) {
            if ($this->manager->enable($name)) {
                $io->writeln(sprintf('✓ <info>%s</info> enabled', $name));
            } else {
                $io->writeln(sprintf('✗ <comment>%s</comment> not found', $name));
            }
        }

        return Command::SUCCESS;
    }
}
