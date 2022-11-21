<?php

namespace App\Validation;

interface DataValidatorInterface
{
    /**
     * @param $entity
     * @return string
     */
    public function isValid($entity):bool;
}