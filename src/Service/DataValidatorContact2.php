<?php

namespace App\Service;

use App\Constants\ConstantsContact;

class DataValidatorContact2 implements DataValidatorInterface
{
    private $errorMessage;

    public function __construct()
    {
        $this->errorMessage = '';
    }

    /**
     * @param string $name
     * @return bool
     */
    private function nameValidation(string $name): bool
    {
        if (strlen($name) < ConstantsContact::NAMEMINLENGTH || strlen($name) > ConstantsContact::NAMEMAXLENGTH) {
            return false;
        }

        return ctype_alpha($name);
    }

    /**
     * @param string $email
     * @return bool
     */
    private function emailValidation(string $email): bool
    {
        for ($i = 0; $i < strlen($email); $i++) {
            if ($email[$i] == '@') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $description
     * @return bool
     */
    private function descriptionValidation(string $description): bool
    {
        if (
            strlen($description) < ConstantsContact::DESCMINLENGTH ||
            strlen($description) > ConstantsContact::DESCMAXLENGTH
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function getErrors(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param $entity
     * @return bool
     */
    public function isValid($entity): bool
    {
        if (!$this->nameValidation($entity->getName())) {
            $this->errorMessage = $this->errorMessage . ConstantsContact::NAMEERROR;
        }

        if (!$this->emailValidation($entity->getEmail())) {
            $this->errorMessage = $this->errorMessage . ConstantsContact::EMAILERROR;
        }

        if (!$this->descriptionValidation($entity->getDescription())) {
            $this->errorMessage = $this->errorMessage . ConstantsContact::DESCERROR;
        }

        return strlen($this->errorMessage) > 0 ? false : true;
    }
}
