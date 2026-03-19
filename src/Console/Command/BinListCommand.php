<?php

declare(strict_types=1);

namespace App\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'bin:list', description: 'List available vendor binaries')]
class BinListCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $binDir = dirname(__DIR__, 4) . '/vendor/bin';

        if (!is_dir($binDir)) {
            $io->comment('No vendor/bin directory found. Run composer install first.');
            return Command::SUCCESS;
        }

        $io->section('Available Binaries');

        foreach (glob($binDir . '/*') as $binary) {
            if (is_executable($binary)) {
                $name = basename($binary);
                $io->writeln(sprintf('  ./vendor/bin/<comment>%s</comment>', $name));
            }
        }

        $io->section('Examples');
        $io->writeln('  ./vendor/bin/moex security:specification SBER');
        $io->writeln('  ./vendor/bin/t-invest portfolio:show');
        $io->writeln('  ./vendor/bin/news news:fetch --ticker SBER');

        return Command::SUCCESS;
    }
}
