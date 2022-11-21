<?php

namespace App\Validation;
use App\Constants\ConstantsContact;

class DataValidatorContact implements DataValidatorInterface
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
    private function nameValidation(string $name):bool
    {

        if(strlen($name)<2 || strlen($name)>10)
            return false;

        return ctype_alpha($name);
    }

    /**
     * @param string $email
     * @return bool
     */
    private function emailValidation(string $email):bool
    {

        for($i=0; $i<strlen($email); $i++)
            if($email[$i] == '@')
                return true;

        return false;
    }

    /**
     * @param string $description
     * @return bool
     */
    private function descriptionValidation(string $description):bool
    {

        if(strlen($description)<10 || strlen($description)>50)
            return false;

        return true;
    }

    /**
     * @param $entity
     * @return string
     */
    public function entityValidation($entity): string
    {
        if(!$this->nameValidation($entity->getName()))
            $this->errorMessage=$this->errorMessage.ConstantsContact::NAMEERROR;

        if(!$this->emailValidation($entity->getEmail()))
            $this->errorMessage=$this->errorMessage.ConstantsContact::EMAILERROR;

        if(!$this->descriptionValidation($entity->getDescription()))
            $this->errorMessage=$this->errorMessage.ConstantsContact::DESCERROR;

        return $this->errorMessage;
    }
}