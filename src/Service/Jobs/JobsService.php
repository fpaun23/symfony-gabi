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

    private int $totalJobs;
    private int $validJobs;
    private int $invalidJobs;
    private int $updatedJobs;
    private int $addedJobs;
    private int $deletedJobs;

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
    }

    public function createJob($dataJob): Jobs
    {
        $job = new Jobs();

        $job->setName($dataJob['name']);
        $job->setDescription($dataJob['description']);
        $job->setPriority($dataJob['priority']);
        $job->setActive($dataJob['active']);
        $job->setCompany($dataJob['company']);
        $job->setCreatedAt(new \DateTime("now"));

        return $job;
    }

    private function getValidData($data): array
    {
        $this->totalJobs = sizeof($data);
        $this->validJobs = 0;
        $this->invalidJobs = 0;
        $jobs = [];

        foreach ($data as $dataJob) {
            $dataJob['company'] = $this->companyRepository->find($dataJob['company_id']);

            if ($this->jobBulkValidator->isValid($dataJob)) {
                $this->validJobs++;
                $jobs[] = $dataJob;
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

        return $jobs;
    }

    private function jobIsInDB($dataJob): ?Jobs
    {
        return $this->jobsRepository->findOneBy(
            [
                'name' => $dataJob['name'],
                'company' => $dataJob['company']
            ]
        );
    }

    private function bulkUpdate($jobs): array
    {
        $updatedJobs = 0;
        $addedJobs = 0;

        foreach ($jobs as $dataJob) {
            $jobFromDB = $this->jobIsInDB($dataJob);

            if ($jobFromDB === null) {
                // need to add job
                $addedJobs++;
                $this->jobsRepository->save($this->createJob($dataJob));
            } else {
                // need to update job
                $updatedJobs++;
                $this->jobsRepository->update($jobFromDB->getId(), $dataJob);
            }
        }

        return [

            "added_jobs" => $addedJobs,
            "updated_jobs" => $updatedJobs
        ];
    }

    private function bulkDelete($jobs): array
    {
        $deletedJobs = 0;

        foreach ($jobs as $dataJob) {
            $jobFromDB = $this->jobIsInDB($dataJob);

            if ($jobFromDB !== null) {
                // need to add job
                $deletedJobs++;
                $this->jobsRepository->remove($jobFromDB);
            }
        }

        return [

            "deleted_jobs" => $deletedJobs
        ];
    }

    private function bulkAdd($jobs): array
    {
        $addedJobs = 0;

        foreach ($jobs as $dataJob) {
            $addedJobs++;
            $this->jobsRepository->save($this->createJob($dataJob));
        }

        return [

            "added_jobs" => $addedJobs
        ];
    }

    public function bulk($notMandatoryParams): array
    {
        $data = $this->fileReader->getData()['jobs'];

        $bulkDeleteState = false;
        $bulkUpdateState = false;

        $addedJobs = 0;
        $updatedJobs = 0;
        $deletedJobs = 0;

        if ($this->jobBulkValidator->notMandatoryParamIsValid("delete", $notMandatoryParams)) {
            $bulkDeleteState = true;
            $deletedJobs = $this->bulkDelete($this->getValidData($data))["deleted_jobs"];
        }

        if ($this->jobBulkValidator->notMandatoryParamIsValid("update", $notMandatoryParams)) {
            $bulkUpdateState = true;
            $bulkUpdate = $this->bulkUpdate($this->getValidData($data));

            $updatedJobs = $bulkUpdate["updated_jobs"];
            $addedJobs = $bulkUpdate["added_jobs"];
        }

        if (!$bulkDeleteState && !$bulkUpdateState) {
            $addedJobs = $this->bulkAdd($this->getValidData($data))["added_jobs"];
        }

        if ($bulkDeleteState && $bulkUpdateState) {
            return [
                "total_jobs" => $this->totalJobs,
                "valid_jobs" => $this->validJobs,
                "invalid_jobs" => $this->invalidJobs,
                "deleted_jobs" => $deletedJobs,
                "updated_jobs" => $updatedJobs,
                "added_jobs" => $addedJobs
            ];
        }

        if ($bulkDeleteState) {
            return [
                "total_jobs" => $this->totalJobs,
                "valid_jobs" => $this->validJobs,
                "invalid_jobs" => $this->invalidJobs,
                "deleted_jobs" => $deletedJobs
            ];
        }

        if ($bulkUpdateState) {
            return [
                "total_jobs" => $this->totalJobs,
                "valid_jobs" => $this->validJobs,
                "invalid_jobs" => $this->invalidJobs,
                "updated_jobs" => $updatedJobs,
                "added_jobs" => $addedJobs
            ];
        }

        return [
            "total_jobs" => $this->totalJobs,
            "valid_jobs" => $this->validJobs,
            "invalid_jobs" => $this->invalidJobs,
            "added_jobs" => $addedJobs
        ];
    }
}
