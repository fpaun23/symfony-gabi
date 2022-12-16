<?php

namespace App\Service;

interface FileReaderInterface
{
    /**
     * @return array
     */
    public function getData(): array;
}