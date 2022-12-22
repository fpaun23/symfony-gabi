<?php

namespace App\Command;

use App\Service\Jobs\JobsService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BulkUpdateCommand extends Command
{
    private JobsService $jobsService;

    public function __construct(JobsService $jobsService)
    {
        $this->jobsService = $jobsService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $data = $this->jobsService->bulk(["update" => 1]);

        foreach ($data as $key => $job) {
            $output->writeln($key . ":" . $job);
        }

        return Command::SUCCESS;
    }
}
