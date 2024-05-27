<?php

namespace App\Command;

use App\Service\LogProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:process-log',
    description: 'Processes a log file from the public directory and stores entries in the database',
)]
class ProcessLogCommand extends Command
{
    public function __construct(private LogProcessor $logProcessor, private KernelInterface $kernel)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('fileName', InputArgument::REQUIRED, 'The name of the log file in the public directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fileName = $input->getArgument('fileName');

        $projectDir = $this->kernel->getProjectDir();
        $filePath = $projectDir . '/public/' . $fileName;

        if (file_exists($filePath)) {
            $this->logProcessor->processLogFile($filePath);
            $io->success('Log file processed successfully.');
            return Command::SUCCESS;
        }

        $io->error('File not found: ' . $filePath);
        return Command::FAILURE;
    }
}
