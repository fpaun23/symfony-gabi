<?php

namespace App\Command;

use App\Service\Jobs\JobsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BulkCommand extends Command
{
    private JobsService $jobsService;

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addArgument('arg2', InputArgument::OPTIONAL, 'Argument description');
    }

    public function __construct(JobsService $jobsService)
    {
        $this->jobsService = $jobsService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $arg1 = $input->getArgument('arg1');
        $arg2 = $input->getArgument('arg2');

        $notMandatoryParams = [
            $arg1 => $arg1,
            $arg2 => $arg2
        ];

        foreach ($notMandatoryParams as $key => $value) {
            echo $key . " " . $value . "\n";
        }

        $data = $this->jobsService->bulk($notMandatoryParams);

        foreach ($data as $key => $job) {
            $output->writeln($key . " " . $job);
        }

        return Command::SUCCESS;
    }
}
