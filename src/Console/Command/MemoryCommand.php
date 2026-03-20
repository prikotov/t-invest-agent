<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Service\Memory\MemoryService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('memory', 'Manage user memory and profile')]
class MemoryCommand extends Command
{
    private const LABELS = [
        'risk_tolerance' => 'Risk Tolerance',
        'horizon' => 'Investment Horizon',
        'style' => 'Investment Style',
        'favorite_sectors' => 'Favorite Sectors',
        'avoid_sectors' => 'Avoid Sectors',
        'max_position_pct' => 'Max Position %',
        'positions_last_sync' => 'Last Sync',
        'updated_at' => 'Last Updated',
    ];

    public function __construct(
        private readonly MemoryService $memoryService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('action', InputArgument::OPTIONAL, 'Action: get|set|update|clear|profile', 'profile')
            ->addArgument('key', InputArgument::OPTIONAL, 'Key for get/set')
            ->addArgument('value', InputArgument::OPTIONAL, 'Value for set')
            ->addOption('risk', null, InputOption::VALUE_OPTIONAL, 'Risk tolerance')
            ->addOption('horizon', null, InputOption::VALUE_OPTIONAL, 'Investment horizon')
            ->addOption('style', null, InputOption::VALUE_OPTIONAL, 'Investment style')
            ->addOption('sectors', null, InputOption::VALUE_OPTIONAL, 'Favorite sectors (comma-separated)')
            ->addOption('max-pos', null, InputOption::VALUE_OPTIONAL, 'Max position %')
            ->setDescription('Manage user memory and profile');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $action = $input->getArgument('action');

        return match ($action) {
            'get' => $this->handleGet($input, $output),
            'set' => $this->handleSet($input, $output),
            'update' => $this->handleUpdate($input, $output),
            'clear' => $this->handleClear($input, $output),
            'profile', 'show' => $this->handleProfile($output),
            default => $this->handleProfile($output),
        };
    }

    private function handleGet(InputInterface $input, OutputInterface $output): int
    {
        $key = $input->getArgument('key');

        if (!$key) {
            $output->writeln('<error>Usage: memory get <key></error>');
            return Command::FAILURE;
        }

        $value = $this->memoryService->get($key);

        if ($value === null) {
            $output->writeln("<error>Key not found: $key</error>");
            $output->writeln('Available keys: ' . implode(', ', $this->memoryService->getAvailableKeys()));
            return Command::FAILURE;
        }

        if (is_array($value)) {
            $output->writeln(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $output->writeln("$key = $value");
        }

        return Command::SUCCESS;
    }

    private function handleSet(InputInterface $input, OutputInterface $output): int
    {
        $key = $input->getArgument('key');
        $value = $input->getArgument('value');

        if (!$key || $value === null) {
            $output->writeln('<error>Usage: memory set <key> <value></error>');
            return Command::FAILURE;
        }

        $this->memoryService->set($key, $value);

        $displayValue = is_array($this->memoryService->get($key))
            ? json_encode($this->memoryService->get($key), JSON_UNESCAPED_UNICODE)
            : $value;
        $output->writeln("<info>Set: $key = $displayValue</info>");

        return Command::SUCCESS;
    }

    private function handleUpdate(InputInterface $input, OutputInterface $output): int
    {
        $options = [];
        foreach (['risk', 'horizon', 'style', 'sectors', 'max-pos'] as $opt) {
            if ($input->getOption($opt) !== null) {
                $options[$opt] = $input->getOption($opt);
            }
        }

        if (empty($options)) {
            $output->writeln('<error>Usage: memory update [--risk=...] [--horizon=...] [--style=...] [--sectors=...] [--max-pos=...]</error>');
            return Command::FAILURE;
        }

        $updated = $this->memoryService->update($options);

        $output->writeln('<info>Profile updated:</info>');
        foreach ($updated as $u) {
            $output->writeln("  - $u");
        }

        return Command::SUCCESS;
    }

    private function handleClear(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->memoryService->isCustomized()) {
            $io->note('Memory is already empty.');
            return Command::SUCCESS;
        }

        $this->memoryService->clear();
        $io->success('Memory cleared. Profile will use defaults.');

        return Command::SUCCESS;
    }

    private function handleProfile(OutputInterface $output): int
    {
        $profile = $this->memoryService->getProfile();
        $isCustomized = $this->memoryService->isCustomized();

        $output->writeln($isCustomized ? '# User Profile' : '# User Profile (defaults)');
        $output->writeln('');

        foreach (self::LABELS as $key => $label) {
            if (!isset($profile[$key])) {
                continue;
            }

            $value = $profile[$key];
            $desc = '';

            if (is_array($value)) {
                $value = empty($value) ? '-' : implode(', ', $value);
            } else {
                $desc = $this->memoryService->getDescription((string) $value) ?? '';
                if ($key === 'max_position_pct') {
                    $desc = 'Макс. доля одной позиции';
                }
            }

            $output->write("**$label:** $value");
            if ($desc) {
                $output->write(" ($desc)");
            }
            $output->writeln('');
        }

        if (!$isCustomized) {
            $output->writeln("\nTo customize, use: <info>memory set <key> <value></info>");
        }

        return Command::SUCCESS;
    }
}
