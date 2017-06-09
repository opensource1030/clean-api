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

        $companyUserImportJobData = [
            "company_id" => isset($data['company_id']) ? $data['company_id'] : '',
            "path" =>isset($data['path']) ? $data['path'] : '',
            "file" =>isset($data['file']) ? $data['file'] : '',
            "total" =>isset($data['total']) ? $data['total'] : '',
            "created" =>isset($data['created']) ? $data['created'] : '',
            "updated" =>isset($data['updated']) ? $data['updated'] : '',
            "failed" =>isset($data['failed']) ? $data['failed'] : '',
            "fields" =>isset($data['fields']) ? serialize($data['fields']) : '',
            "sample" =>isset($data['sample']) ? serialize($data['sample']) : '',
            "mappings" => isset($data['mappings']) ? serialize($data['mappings']) : serialize(new \stdClass),
            "status" => static::STATUS_PENDING,
            "created_by_id" =>isset($data['created_by_id']) ? $data['created_by_id'] : '',
            "updated_by_id" =>isset($data['updated_by_id']) ? $data['updated_by_id'] : '',
        ];

        $companyUserImportJob = $this->model->create($companyUserImportJobData);

        if (!$companyUserImportJob) {
            return false;
        }

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
        $companyUserImportJob = $this->model->find($data['id']);

        if (!$companyUserImportJob) {
            return false;
        }


        //@TODO4Carlos: poner adecuadamente los valores también


        $companyUserImportJob->companyId = $data["id"];
        $companyUserImportJob->companyId = $data["id"];
        $companyUserImportJob->companyId = $data["id"];
        $companyUserImportJob->companyId = $data["id"];
        $companyUserImportJob->companyId = $data["id"];
        $companyUserImportJob->companyId = $data["id"];
        $companyUserImportJob->companyId = $data["id"];
        $companyUserImportJob->companyId = $data["id"];
        $companyUserImportJob->companyId = $data["id"];
        $companyUserImportJob->companyId = $data["id"];


        /*
        $company->name = isset($data['name']) ? $data['name'] : null;
        $company->label = isset($data['label']) ? $data['label'] : null;
        $company->shortName = isset($data['shortName']) ? $data['shortName'] : 'shortName';
        $company->active = isset($data['active']) ? $data['active'] : 0;
        // $company->isLive = isset($data['isLive']) ? $data['isLive'] : 0;
        $company->isCensus = isset($data['isCensus']) ? $data['isCensus'] : 0;
        $company->assetPath = isset($data['assetPath']) ? $data['assetPath'] : '/ACME/WA';
        $company->active = isset($data['active']) ? $data['active'] : 0;
        $company->currentBillMonth = isset($data['currentBillMonth']) ? $data['currentBillMonth'] : null;
        $company->defaultLocation = isset($data['defaultLocation']) ? $data['defaultLocation'] : null;
        //*/


        if (!$companyUserImportJob->save()) {
            return false;
        }
        
        return $companyUserImportJob;
    }

}
