<?php

declare(strict_types=1);

namespace App\Console\Command\Skill;

use App\Service\Skill\Manager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'skill:disable', description: 'Disable specific skill(s)')]
class DisableCommand extends Command
{
    public function __construct(
        private readonly Manager $manager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('skills', InputArgument::IS_ARRAY, 'Skill names to disable')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Target agent: opencode, kilocode, all', 'opencode');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $skills = $input->getArgument('skills');

        if (empty($skills)) {
            $io->error('Specify skill name(s): agent skill:disable <skill>');
            return Command::FAILURE;
        }

        $target = $input->getOption('target');
        $targets = $target === 'all' ? Manager::getValidTargets() : [$target];

        foreach ($targets as $t) {
            foreach ($skills as $name) {
                if ($this->manager->disable($name, $t)) {
                    $io->writeln(sprintf('[%s] <info>%s</info> disabled', $t, $name));
                } else {
                    $io->writeln(sprintf('[%s] <comment>%s</comment> not enabled', $t, $name));
                }
            }
        }

        return Command::SUCCESS;
    }
}
