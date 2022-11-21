<?php

namespace App\Validation;

use App\Constants\ConstantsContact;

class DataValidatorContact implements DataValidatorInterface
{
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
     * @param $entity
     * @return string
     */
    public function getErrors($entity): string
    {
        $errorMessage = '';

        if (!$this->nameValidation($entity->getName())) {
            $errorMessage = $errorMessage . ConstantsContact::NAMEERROR;
        }

        if (!$this->emailValidation($entity->getEmail())) {
            $errorMessage = $errorMessage . ConstantsContact::EMAILERROR;
        }

        if (!$this->descriptionValidation($entity->getDescription())) {
            $errorMessage = $errorMessage . ConstantsContact::DESCERROR;
        }

        return $errorMessage;
    }

    /**
     * @param $entity
     * @return bool
     */
    public function isValid($entity): bool
    {
        if (!$this->nameValidation($entity->getName())) {
            return false;
        }

        if (!$this->emailValidation($entity->getEmail())) {
            return false;
        }

        if (!$this->descriptionValidation($entity->getDescription())) {
            return false;
        }

        return true;
    }
}
