<?php

namespace WA\Jobs;

use WA\DataStore\Company\CompanyUserImportJob;
use WA\DataStore\Company\CompanyUserImportJobTransformer;
use WA\Helpers\Vendors\CSVParser;

class ImportBulkUsersJob extends Job
{
    protected $jobId;
    protected $companyUserImportJob;

    /**
     * ImportBulkUsersJob constructor.
     * @param $jobId
     */
    public function __construct($jobId, $companyUserImportJob)
    {
        $this->jobId = $jobId;
        $this->companyUserImportJob = $companyUserImportJob;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // $companyUserImportJob = app()->make('WA\Repositories\Company\CompanyUserImportJobInterface');
        // $jobFound = CompanyUserImportJob::find($this->jobId);
        $jobFound = $this->companyUserImportJob;
        
        // Job must be in PENDING:
        if(($jobFound->status === CompanyUserImportJobTransformer::STATUS_PENDING)) {
            // Okay
        } else {
            // Cut the job:
            \Log::debug("The job should be in PENDING status to be executed.");
            return null;
        }

        $data = [
            'id' => $this->jobId,
            'status' => CompanyUserImportJobTransformer::STATUS_WORKING
        ];
        $job = $jobFound->update($data);

        // start importing/updating
        $filePath = $job->filepath;
        /*
        // storage_path() . DIRECTORY_SEPARATOR . $job->path;
        // . DIRECTORY_SEPARATOR . $job->file;
        //*/
        $csvParser = new CSVParser($filePath);
        $rows = $csvParser->getRows(true);

        foreach($rows as $index => $row) {
            if($index == 0) continue;
            if(empty($row)) continue;
            if(join('', $row) == '') continue;
            $formattedRow = $this->getFormatRow($rows[0], $row);
            // check email is empty,
            // then increase failed and continue
            if(empty($formattedRow->email)) {
                $job->failedUsers = min($job->failedUsers + 1, $job->totalUsers);
                $job->save();

                continue;
            }
            // check if email is already exist
            // if exist, update it
            // if not, create new one
            $userInterface = app()->make('WA\Repositories\User\UserInterface');
            $user = $userInterface->byEmail($formattedRow->email);
            $mappings = unserialize($job->mappings);
            $data = $this->makeMappingRow($mappings, $formattedRow);
            if($user == null) {
                \Log::debug("Data for user UPDATE: ");
                \Log::debug(json_encode($data, JSON_PRETTY_PRINT));
                $result = $userInterface->create($data);
                if($result == false) {
                    $job->failedUsers = min($job->failedUsers + 1, $job->totalUsers);
                } else {
                    $job->createdUsers = min($job->createdUsers + 1, $job->totalUsers);
                }
            } else {
                $data['id'] = $user->id;
                \Log::debug("Data for user CREATE: ");
                \Log::debug(json_encode($data, JSON_PRETTY_PRINT));
                $result = $userInterface->update($data);
                if($result == 'notSaved') {
                    $job->failedUsers = min($job->failedUsers + 1, $job->totalUsers);
                } else {
                    $job->updatedUsers = min($job->updatedUsers + 1, $job->totalUsers);
                }
            }
            $job->save();
        }
        $data = [
            'id' => $this->jobId,
            'status' => CompanyUserImportJobTransformer::STATUS_COMPLETED
        ];
        $jobUpdated = $jobFound->update($data);
    }

    private function getFormatRow($header, $row) {
        $result = new \stdClass();
        foreach($header as $index => $field) {
            if(isset($row[$index])) {
                $result->$field = $row[$index];
            } else {
                $result->$field = '';
            }
        }

        return $result;
    }

    private function makeMappingRow($mappings, $row) {
        $result = array();

        foreach($mappings as $mapping) {
            $field = $mapping['csvField'];
            $result[$mapping['dbField']] = $row->$field;
        }

        return $result;
    }
}
