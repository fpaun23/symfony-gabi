<?php

namespace App\Validators;

use Doctrine\ORM\Mapping\Entity;

interface ValidatorInterface
{
    public function isValid(array $data): bool;

    public function getErrorMessage(): string;
}