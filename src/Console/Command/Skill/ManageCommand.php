<?php

declare(strict_types=1);

namespace App\Console\Command\Skill;

use App\Service\Skill\Manager;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'skill:manage', description: 'Interactive skill manager')]
final class ManageCommand extends Command
{
    public function __construct(
        private readonly Manager $manager
    ) {
        parent::__construct();
    }

    #[Override]
    protected function configure(): void
    {
        $this
            ->addOption('enable', 'e', InputOption::VALUE_OPTIONAL, 'Enable specific skills (comma-separated)')
            ->addOption('disable', 'd', InputOption::VALUE_OPTIONAL, 'Disable specific skills (comma-separated)')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Enable all skills')
            ->addOption('none', null, InputOption::VALUE_NONE, 'Disable all skills')
            ->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Target agent: opencode, kilocode, kilo, all', 'opencode');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $target = $input->getOption('target');
        $targets = $this->resolveTargets($target, $io);

        if ($targets === null) {
            return Command::FAILURE;
        }

        $skills = $this->manager->getAvailableSkills();

        if (empty($skills)) {
            $io->comment('No skills found in skills/ directory.');
            return Command::SUCCESS;
        }

        foreach ($targets as $t) {
            $enabled = $this->manager->getEnabledSkills($t);

            if ($input->getOption('all')) {
                $this->enableAll($io, $skills, $t);
                continue;
            }

            if ($input->getOption('none')) {
                $this->disableAll($io, $enabled, $t);
                continue;
            }

            if ($input->getOption('enable')) {
                $this->enableSelected($io, $input->getOption('enable'), $t);
                continue;
            }

            if ($input->getOption('disable')) {
                $this->disableSelected($io, $input->getOption('disable'), $t);
                continue;
            }

            $this->interactive($input, $output, $io, $skills, $enabled, $t);
        }

        return Command::SUCCESS;
    }

    private function interactive(
        InputInterface $input,
        OutputInterface $output,
        SymfonyStyle $io,
        array $skills,
        array $enabled,
        string $target,
    ): int {
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
        $io->writeln(sprintf('Target: <info>%s</info>', $target));
        $io->writeln('Select skills to enable (space to toggle, enter to confirm):');
        $io->newLine();

        $helper = $this->getHelper('question');
        assert($helper instanceof QuestionHelper);
        $question = new ChoiceQuestion('Skills:', $choices, implode(',', $defaults));
        $question->setMultiselect(true);

        $selected = $helper->ask($input, $output, $question);

        $enabledCount = 0;
        $disabledCount = 0;

        foreach (array_keys($skills) as $name) {
            if (in_array($name, $selected, true)) {
                if ($this->manager->enable($name, $target)) {
                    $enabledCount++;
                }
            } else {
                if ($this->manager->disable($name, $target)) {
                    $disabledCount++;
                }
            }
        }

        $io->newLine();
        $io->success(sprintf('Enabled: %d, Disabled: %d', $enabledCount, $disabledCount));

        return Command::SUCCESS;
    }

    private function enableAll(SymfonyStyle $io, array $skills, string $target): void
    {
        $count = 0;
        foreach ($skills as $name => $skill) {
            if ($this->manager->enable($name, $target)) {
                $count++;
            }
        }
        $io->success(sprintf('[%s] Enabled %d skill(s)', $target, $count));
    }

    private function disableAll(SymfonyStyle $io, array $enabled, string $target): void
    {
        $count = 0;
        foreach (array_keys($enabled) as $name) {
            if ($this->manager->disable($name, $target)) {
                $count++;
            }
        }
        $io->success(sprintf('[%s] Disabled %d skill(s)', $target, $count));
    }

    private function enableSelected(SymfonyStyle $io, string $list, string $target): void
    {
        $skills = array_map('trim', explode(',', $list));
        $count = 0;
        foreach ($skills as $name) {
            if ($this->manager->enable($name, $target)) {
                $count++;
            }
        }
        $io->success(sprintf('[%s] Enabled %d skill(s)', $target, $count));
    }

    private function disableSelected(SymfonyStyle $io, string $list, string $target): void
    {
        $skills = array_map('trim', explode(',', $list));
        $count = 0;
        foreach ($skills as $name) {
            if ($this->manager->disable($name, $target)) {
                $count++;
            }
        }
        $io->success(sprintf('[%s] Disabled %d skill(s)', $target, $count));
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
