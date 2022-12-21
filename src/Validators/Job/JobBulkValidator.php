<?php

namespace App\Validators\Job;

use App\Entity\Company;
use App\Validators\ValidatorInterface;
use App\Constants\JobsConstants;

class JobBulkValidator implements ValidatorInterface
{
    private string $errorMessage;

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
        $this->errorMessage = '';

        if (!$this->paramsAreValid($data)) {
            $this->errorMessage = "Params name are not valid";
            return false;
        }

        if (!$this->nameIsValid($data['name'])) {
            $this->errorMessage = $this->errorMessage . "Invalid name\n";
        }

        if (!$this->descriptionIsValid($data['description'])) {
            $this->errorMessage = $this->errorMessage . "Invalid description\n";
        }

        if (!$this->companyIdIsValid($data['company_id'])) {
            $this->errorMessage = $this->errorMessage . "Invalid company id\n";
        }

        if (!$this->activeIsValid($data['active'])) {
            $this->errorMessage = $this->errorMessage . "Invalid active\n";
        }

        if (!$this->priorityIsValid($data['priority'])) {
            $this->errorMessage = $this->errorMessage . "Invalid priority\n";
        }

        return $this->errorMessage == '';
    }

    public function updateIsValid($update): bool
    {
        return ($update == 1 || $update == 0);
    }

    public function deleteIsValid($delete): bool
    {
        return ($delete == 1 || $delete == 0);
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
