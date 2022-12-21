<?php

namespace App\Service\Jobs;

use App\Entity\Jobs;
use App\Repository\CompanyRepository;
use App\Repository\JobsRepository;
use App\Service\File\FileReaderInterface;
use App\Validators\Job\JobBulkValidator;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class JobsService
{
    private FileReaderInterface $fileReader;
    private JobBulkValidator $jobBulkValidator;
    private LoggerInterface $logger;
    private JobsRepository $jobsRepository;
    private CompanyRepository $companyRepository;

    private int $totalJobs;
    private int $validJobs;
    private int $invalidJobs;

    public function __construct(
        LoggerInterface $logger,
        FileReaderInterface $fileReader,
        JobBulkValidator $jobBulkValidator,
        JobsRepository $jobsRepository,
        CompanyRepository $companyRepository
    ) {
        $this->logger = $logger;
        $this->fileReader = $fileReader;
        $this->jobBulkValidator = $jobBulkValidator;
        $this->jobsRepository = $jobsRepository;
        $this->companyRepository = $companyRepository;

        $this->totalJobs = 0;
        $this->validJobs = 0;
        $this->invalidJobs = 0;

        $this->update = 0;
        $this->delete = 0;
    }

    public function bulk($mandatoryParams): array
    {
        if ($this->jobBulkValidator->deleteIsValid($mandatoryParams['delete'])) {
            $this->delete = $mandatoryParams['delete'];
        }
        if ($this->jobBulkValidator->updateIsValid($mandatoryParams['update'])) {
            $this->update = $mandatoryParams['update'];
        }

        $data = $this->fileReader->getData()['jobs'];
        $this->totalJobs = sizeof($data);

        foreach ($data as $dataJob) {
            if ($this->jobBulkValidator->isValid($dataJob)) {
                $company = $this->companyRepository->find($dataJob['company_id']);

                if (!$this->jobBulkValidator->companyIsValid($company)) {
                    $this->invalidJobs++;

                    $this->logger->error(
                        "Error",
                        [
                            json_encode("Company with id " . $dataJob['company_id'] . " does not exist")
                        ]
                    );
                } else {
                    // we can create and add this job

                    $this->validJobs++;

                    $job = new Jobs();

                    $job->setName($dataJob['name']);
                    $job->setDescription($dataJob['description']);
                    $job->setPriority($dataJob['priority']);
                    $job->setActive($dataJob['active']);
                    $job->setCompany($company);
                    $job->setCreatedAt(new \DateTime("now"));

                    $this->jobsRepository->save($job);

                    $this->logger->notice(
                        "Successful",
                        [
                            json_encode(
                                [
                                    'name' => $dataJob['name'],
                                    'description' => $dataJob['description'],
                                    'company_id' => $dataJob['company_id'],
                                    'active' => $dataJob['active'],
                                    'priority' => $dataJob['priority']
                                ]
                            )
                        ]
                    );
                }
            } else {
                $this->invalidJobs++;

                $this->logger->error(
                    "Error",
                    [
                        json_encode($this->jobBulkValidator->getErrorMessage())
                    ]
                );
            }
        }

        return [

            "total_jobs" => $this->totalJobs,
            "valid_jobs" => $this->validJobs,
            "invalid_jobs" => $this->invalidJobs
        ];
    }
}
