<?php

namespace WA\DataStore\Company;

use WA\DataStore\FilterableTransformer;
use League\Fractal\Resource\Collection as ResourceCollection;

use WA\DataStore\Company\Company;


/**
 * Class CompanyUserImportJobTransformer.
 */
class CompanyUserImportJobTransformer extends FilterableTransformer
{

    protected $availableIncludes = [
        'companies'
    ];


    /**
     * @param CompanyUserImportJob $companyUserImportJob
     *
     * @return array
     */
    public function transform(CompanyUserImportJob $companyUserImportJob)
    {
        return [
            "id"                => (int)$companyUserImportJob->id,
            "jobType"           => $companyUserImportJob->jobType,
            "companyId"         => (int)$companyUserImportJob->companyId,
            "path"              => $companyUserImportJob->filepath,
            "file"              => $companyUserImportJob->filename,
            "totalUsers"        => (int)$companyUserImportJob->totalUsers,
            "createdUsers"      => (int)$companyUserImportJob->createdUsers,
            "creatableUsers"    => (int)$companyUserImportJob->creatableUsers,
            "updatedUsers"      => (int)$companyUserImportJob->updatedUsers,
            "updatableUsers"    => (int)$companyUserImportJob->updatableUsers,
            "failedUsers"       => (int)$companyUserImportJob->failedUsers,
            "CSVfields"         => unserialize($companyUserImportJob->fields),
            "DBfields"          => $companyUserImportJob->dbfields,
            "sampleUser"        => unserialize($companyUserImportJob->sampleUser),
            "mappings"          => unserialize($companyUserImportJob->mappings),
            "status"            => $this->getStatusText($companyUserImportJob->status),
            "created_by_id"     => (int)$companyUserImportJob->created_by_id,
            "updated_by_id"     => (int)$companyUserImportJob->updated_by_id,
            "created_at"        => $companyUserImportJob->created_at,
            "updated_at"        => $companyUserImportJob->updated_at
        ];
    }

    const STATUS_PENDING = 0;
    const STATUS_WORKING = 1;
    const STATUS_SUSPENDED = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;

    /**
     * get status text
     *
     * @return string
     */
    public function getStatusText($status) {
        if($status == static::STATUS_PENDING) {
            return 'Pending';
        } else if($status == static::STATUS_WORKING) {
            return 'Working';
        } else if($status == static::STATUS_SUSPENDED) {
            return 'Suspended';
        } else if($status == static::STATUS_COMPLETED) {
            return 'Completed';
        } else if($status == static::STATUS_CANCELED) {
            return 'Canceled';
        }
    }

    



}



























