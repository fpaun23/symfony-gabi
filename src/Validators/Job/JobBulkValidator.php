<?php

namespace App\Validators\Job;

use App\Entity\Company;
use App\Validators\ValidatorInterface;
use App\Constants\JobsConstants;

class JobBulkValidator implements ValidatorInterface
{
    private function nameIsValid(string $name): bool
    {
        return ($name != null && strlen($name) >= 2);
    }

    private function descriptionIsValid(string $name): bool
    {
        return ($name != null && strlen($name) >= 2);
    }

    private function companyIdIsValid(int $companyId): bool
    {
        return ($companyId >= 1);
    }

    private function activeIsValid(int $active): bool
    {
        return ($active == 0 || $active == 1);
    }

    private function priorityIsValid(int $priority): bool
    {
        return ($priority >= 0 && $priority <= 9);
    }

    private function paramsAreValid(array $params): bool
    {
        foreach (JobsConstants::MANDATORY_JOB_ARRAY_KEYS as $key) {
            if (!array_key_exists($key, $params)) {
                return false;
            }
        }

        foreach (JobsConstants::MANDATORY_JOB_ARRAY_KEYS as $key) {
            if ($params[$key] === '') {
                return false;
            }
        }

        return true;
    }

    public function companyIsValid(?Company $company): bool
    {
        return ($company !== null);
    }

    public function isValid(array $data): bool
    {
        if (!$this->paramsAreValid($data)) {
            return false;
        }

        return (
            $this->nameIsValid($data['name']) &&
            $this->descriptionIsValid($data['description']) &&
            $this->companyIdIsValid($data['company_id']) &&
            $this->activeIsValid($data['active']) &&
            $this->priorityIsValid($data['priority'])
        );
    }
}
