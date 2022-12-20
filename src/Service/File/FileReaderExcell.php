<?php

namespace App\Service\File;

use App\Constants\JobsConstants;
use SimpleExcel\SimpleExcel;
use Symfony\Component\Form\Exception\InvalidArgumentException;

class FileReaderExcell implements FileReaderInterface
{
    private SimpleExcel $excel;

    public function __construct()
    {
        $this->excel = new SimpleExcel('csv');
    }

    /**
     * @return int
     */
    private function getNumberOfExcelRows(): int
    {
        $row = 2;

        while ($this->excel->parser->isRowExists($row)) {
            $row++;
        }

        return $row;
    }

    private function getRowArray($row)
    {
        return $this->excel->parser->getRow($row);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        if (!file_exists(JobsConstants::FILE_PATH)) {
            throw new InvalidArgumentException("File does not exist");
        }

        $this->excel->parser->loadFile(JobsConstants::FILE_PATH);

        $numberOfJobs = $this->getNumberOfExcelRows();

        $jobs = [];

        for ($row = 2; $row < $numberOfJobs; $row++) {
            $columnArray = $this->getRowArray($row);

            $jobName = $columnArray[0];
            $jobDescription = $columnArray[1];
            $jobCompanyId = $columnArray[2];
            $jobActive = $columnArray[3];
            $jobPriority = $columnArray[4];

            $jobs[] = [

                "name" => $jobName,
                "description" => $jobDescription,
                "company_id" => $jobCompanyId,
                "active" => $jobActive,
                "priority" => $jobPriority
            ];
        }

        return $jobs;
    }
}
