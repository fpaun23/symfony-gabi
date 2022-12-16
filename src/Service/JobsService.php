<?php

namespace App\Service;

class JobsService
{
    private FileReaderInterface $fileReader;

    public function __construct(FileReaderInterface $fileReader)
    {
        $this->fileReader = $fileReader;
    }

}