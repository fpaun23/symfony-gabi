<?php

namespace App\Service;

use App\Entity\Jobs;
use SimpleExcel\SimpleExcel;

class FileReaderExcell implements FileReaderInterface
{
    private $excel;

    public function __construct()
    {
        $file_path = "../src/Controller/test.csv";
        $this->excel = new SimpleExcel('csv');
        $this->excel->parser->loadFile($file_path);
    }

    /**
     * @param $excel
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

    private function getNumberOfFirstRowCells(): int
    {
        return sizeof($this->excel->parser->getRow(1));
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
        $numberOfParams = $this->getNumberOfFirstRowCells();
        $numberOfJobs = $this->getNumberOfExcelRows();

        echo $numberOfParams . " " . $numberOfJobs;

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