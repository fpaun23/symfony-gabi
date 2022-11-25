<?php

namespace App\Service;

interface DataValidatorInterface
{
    /**
     * @param $entity
     * @return bool
     */
    public function isValid($entity): bool;

    /**
     * @return string
     */
    public function getErrors(): string;
}
