<?php

declare(strict_types=1);

namespace App\Console\Command\Skill;

use App\Service\Skill\Manager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'skill:manage', description: 'Interactive skill manager')]
class ManageCommand extends Command
{
    public function __construct(
        private readonly Manager $manager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('enable', 'e', InputOption::VALUE_OPTIONAL, 'Enable specific skills (comma-separated)')
            ->addOption('disable', 'd', InputOption::VALUE_OPTIONAL, 'Disable specific skills (comma-separated)')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Enable all skills')
            ->addOption('none', null, InputOption::VALUE_NONE, 'Disable all skills');
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

        if ($input->getOption('all')) {
            return $this->enableAll($io, $skills);
        }

        if ($input->getOption('none')) {
            return $this->disableAll($io, $enabled);
        }

        if ($input->getOption('enable')) {
            return $this->enableSelected($io, $input->getOption('enable'));
        }

        if ($input->getOption('disable')) {
            return $this->disableSelected($io, $input->getOption('disable'));
        }

        return $this->interactive($input, $output, $io, $skills, $enabled);
    }

    private function interactive(InputInterface $input, OutputInterface $output, SymfonyStyle $io, array $skills, array $enabled): int
    {
        $choices = [];
        $defaults = [];

        foreach ($skills as $name => $skill) {
            $label = sprintf('%s — %s', $name, $skill['description'] ?: 'no description');
            $choices[$name] = $label;
            if (isset($enabled[$name])) {
                $defaults[] = $name;
            }
        }

        $io->newLine();
        $io->writeln('Select skills to enable (space to toggle, enter to confirm):');
        $io->newLine();

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Skills:', $choices, implode(',', $defaults));
        $question->setMultiselect(true);

        $selected = $helper->ask($input, $output, $question);

        $enabledCount = 0;
        $disabledCount = 0;

        foreach (array_keys($skills) as $name) {
            if (in_array($name, $selected, true)) {
                if ($this->manager->enable($name)) {
                    $enabledCount++;
                }
            } else {
                if ($this->manager->disable($name)) {
                    $disabledCount++;
                }
            }
        }

        $io->newLine();
        $io->success(sprintf('Enabled: %d, Disabled: %d', $enabledCount, $disabledCount));

        return Command::SUCCESS;
    }

    private function enableAll(SymfonyStyle $io, array $skills): int
    {
        $count = 0;
        foreach ($skills as $name => $skill) {
            if ($this->manager->enable($name)) {
                $count++;
            }
        }
        $io->success(sprintf('Enabled %d skill(s)', $count));
        return Command::SUCCESS;
    }

    private function disableAll(SymfonyStyle $io, array $enabled): int
    {
        $count = 0;
        foreach (array_keys($enabled) as $name) {
            if ($this->manager->disable($name)) {
                $count++;
            }
        }
        $io->success(sprintf('Disabled %d skill(s)', $count));
        return Command::SUCCESS;
    }

    private function enableSelected(SymfonyStyle $io, string $list): int
    {
        $skills = array_map('trim', explode(',', $list));
        $count = 0;
        foreach ($skills as $name) {
            if ($this->manager->enable($name)) {
                $count++;
            }
        }
        $io->success(sprintf('Enabled %d skill(s)', $count));
        return Command::SUCCESS;
    }

    private function disableSelected(SymfonyStyle $io, string $list): int
    {
        $skills = array_map('trim', explode(',', $list));
        $count = 0;
        foreach ($skills as $name) {
            if ($this->manager->disable($name)) {
                $count++;
            }
        }
        $io->success(sprintf('Disabled %d skill(s)', $count));
        return Command::SUCCESS;
    }
}
