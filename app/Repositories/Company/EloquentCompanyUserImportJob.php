<?php

namespace WA\Repositories\Company;

use Illuminate\Database\Eloquent\Model;
use Log;
use WA\Repositories\AbstractRepository;
use WA\DataStore\Company\CompanyUserImportJob;

class EloquentCompanyUserImportJob extends AbstractRepository implements CompanyUserImportJobInterface
{

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @param Model            $model
     */
    public function __construct(Model $model) {
        $this->model = $model;
    }

    /*
     * Create a New CompanyUserImportJob
     * @param array     $data
     *
     * @return Object of the companyUserImportJob | false
     */
    public function create(
        array $data
    ) {
        if(!isset($data['companyId'])) {
            \Log::debug("CompanyUserImportJob needs a companyId to be created.");
            return false;
        }
        
        $companyUserImportJobData = [
            "jobType" =>isset($data['jobType']) ? $data['jobType'] : '',
            "companyId" => $data['companyId'],
            "filepath" =>isset($data['filepath']) ? $data['filepath'] : '',
            "filename" =>isset($data['filename']) ? $data['filename'] : '',
            "totalUsers" =>isset($data['totalUsers']) ? $data['totalUsers'] : 0,
            "createdUsers" =>isset($data['createdUsers']) ? $data['createdUsers'] : 0,
            "creatableUsers" =>isset($data['creatableUsers']) ? $data['creatableUsers'] : 0,
            "updatedUsers" =>isset($data['updatedUsers']) ? $data['updatedUsers'] : 0,
            "updatableUsers" =>isset($data['updatableUsers']) ? $data['updatableUsers'] : 0,
            "failedUsers" =>isset($data['failedUsers']) ? $data['failedUsers'] : 0,
            "fields" =>isset($data['fields']) ? serialize($data['fields']) : '',
            "sampleUser" =>isset($data['sampleUser']) ? serialize($data['sampleUser']) : '',
            "mappings" => isset($data['mappings']) ? serialize($data['mappings']) : '',
            "status" => static::STATUS_PENDING,
            "created_by_id" =>isset($data['created_by_id']) ? $data['created_by_id'] : 0,
            "updated_by_id" =>isset($data['updated_by_id']) ? $data['updated_by_id'] : 0
        ];

        $companyUserImportJob = $this->model->create($companyUserImportJobData);

        return $companyUserImportJob;
    }

    /**
     * Delete a Company.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true)
    {
        if (!$this->model->find($id)) {
            return false;
        }

        if (!$soft) {
            $this->model->forceDelete($id);
        }

        return $this->model->destroy($id);
    }

    /**
     * Update a companyUserImportJob.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {

        //\Log::debug("Datos para el update:");
        //\Log::debug(json_encode($data, JSON_PRETTY_PRINT));

        $companyUserImportJob = $this->model->find($data['id']);

        if (!$companyUserImportJob) {
            \Log::debug("CompanyUserImportJob could NOT be found.");
            return false;
        }

        if (isset($data['jobType'])) {
            $companyUserImportJob->jobType = $data['jobType'];
        }

        if (isset($data['companyId'])) {
            $companyUserImportJob->companyId = $data['companyId'];
        }

        if (isset($data['filepath'])) {
            $companyUserImportJob->filepath = $data['filepath'];
        }

        if (isset($data['filename'])) {
            $companyUserImportJob->filename = $data['filename'];
        }

        if (isset($data['totalUsers'])) {
            $companyUserImportJob->totalUsers = $data['totalUsers'];
        }

        if (isset($data['createdUsers'])) {
            $companyUserImportJob->createdUsers = $data['createdUsers'];
        }

        if (isset($data['creatableUsers'])) {
            $companyUserImportJob->creatableUsers = $data['creatableUsers'];
        }

        if (isset($data['updatedUsers'])) {
            $companyUserImportJob->updatedUsers = $data['updatedUsers'];
        }

        if (isset($data['updatableUsers'])) {
            $companyUserImportJob->updatableUsers = $data['updatableUsers'];
        }

        if (isset($data['failedUsers'])) {
            $companyUserImportJob->failedUsers = $data['failedUsers'];
        }

        if (isset($data['sampleUser'])) {
            $companyUserImportJob->sampleUser = serialize($data['sampleUser']);
        }

        if (isset($data['mappings'])) {
            $companyUserImportJob->mappings = serialize($data['mappings']);
        }

        if (isset($data['updated_by_id'])) {
            $companyUserImportJob->updated_by_id = $data['updated_by_id'];
        }

        if (isset($data['status'])) {
            $companyUserImportJob->status = $data['status'];
        }

        // $companyUserImportJob->status = static::STATUS_WORKING;

        if (!$companyUserImportJob->save()) {
            \Log::debug("CompanyUserImportJob could NOT be saved.");
            return false;
        }
        
        return $companyUserImportJob;

    }

    /**
     * @param $companyId
     *
     * @return Mappings of the CompanyUserImportJob related to companyId
     */
    public function getMappingsByCompanyId($companyId)
    {
        $response = $this->model->where('companyId', $companyId)->orderBy('created_at', 'desc')->get();

        foreach ($response as $key => $value) {
            if($value->mappings != '') {
                return $value->mappings;
            }
        }

        return new \StdClass;
    }

    /**
     * Retrieve the filters for the Model.
     *
     * @param int  $companyId
     *
     * @return Array
     */
    public function addFilterToTheRequest($companyId) {
        $aux['companyId'] = (string) $companyId;
        return $aux;
    }
}
