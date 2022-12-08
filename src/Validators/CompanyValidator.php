<?php

namespace App\Validators;

use Symfony\Component\Form\Exception\InvalidArgumentException;

class CompanyValidator
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
}