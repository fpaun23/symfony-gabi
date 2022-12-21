<?php

namespace App\Validators;

use App\Entity\Company;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class JobValidator
{

    public function idIsValid(int $id): void
    {
        if ($id < 1) {
            throw new InvalidArgumentException("Id is invalid!");
        }
    }

    public function nameIsValid(string $name): void
    {
        if ($name == null || strlen($name) < 2) {
            throw new InvalidArgumentException("Name is invalid!");
        }
    }

    public function descriptionIsValid(string $name): void
    {
        if ($name == null || strlen($name) < 2) {
            throw new InvalidArgumentException("Description is invalid!");
        }
    }

    public function companyIsValid(?Company $company): void
    {
        if ($company == null) {
            throw new InvalidArgumentException("Company is invalid!");
        }
    }

    public function paramsIsValid(array $params): bool
    {
        if (
            !array_key_exists('name', $params) ||
            !array_key_exists('description', $params) ||
            !array_key_exists('company_id', $params) ||
            !array_key_exists('active', $params) ||
            !array_key_exists('priority', $params)
        ) {
            return false;
        }

        if (
            $params['name'] === '' ||
            $params['description'] === '' ||
            $params['company_id'] === '' ||
            $params['active'] === '' ||
            $params['priority'] === ''
        ) {
            return false;
        }

        return true;
    }
}
