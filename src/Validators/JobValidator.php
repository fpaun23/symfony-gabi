<?php

namespace App\Validators;

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
}