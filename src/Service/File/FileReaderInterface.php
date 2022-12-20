<?php

namespace App\Service\File;

interface FileReaderInterface
{
    /**
     * @return array
     */
    public function getData(): array;
}