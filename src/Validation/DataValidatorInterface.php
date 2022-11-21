<?php

namespace App\Validation;

interface DataValidatorInterface
{
    /**
     * @param $entity
     * @return bool
     */
    public function isValid($entity): bool;
}
