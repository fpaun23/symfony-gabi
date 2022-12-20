<?php

namespace App\Service\Jobs;

use App\Entity\Jobs;
use App\Repository\CompanyRepository;
use App\Repository\JobsRepository;
use App\Service\File\FileReaderInterface;
use App\Validators\Job\JobBulkValidator;
use Psr\Log\LoggerInterface;

class JobsService
{
    private FileReaderInterface $fileReader;
    private JobBulkValidator $jobBulkValidator;
    private LoggerInterface $logger;
    private JobsRepository $jobsRepository;
    private CompanyRepository $companyRepository;

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
    }

    public function bulk(): void
    {
        $data = $this->fileReader->getData();

        foreach ($data as $dataJob) {
            if ($this->jobBulkValidator->isValid($dataJob)) {
                $company = $this->companyRepository->find($dataJob['company_id']);

                if (!$this->jobBulkValidator->companyIsValid($company)) {
                    $this->logger->error(
                        "Error",
                        [
                            json_encode("Company does not exist")
                        ]
                    );
                } else {
                    // we can create and add this job

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
                $this->logger->error(
                    "Error",
                    [
                        json_encode("Invalid job")
                    ]
                );
            }
        }
    }
}
