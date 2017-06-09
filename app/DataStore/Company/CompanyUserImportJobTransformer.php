<?php

namespace WA\DataStore\Company;

use WA\DataStore\FilterableTransformer;
use League\Fractal\Resource\Collection as ResourceCollection;

use WA\DataStore\Company\Company;
use WA\DataStore\User\User;

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
        $dbFields = [];

        // 1. Add the Database Fields of the table, just hardcoded:
        /*
        $user = User::where('companyId', $companyUserImportJob['company_id'])->first();
        $dbFields_1 = $user->getFillable();
        $dbFields_1 = $this->filterUnnecessaryFields($dbFields_1);
        //*/
        $dbFields_1 = [
            "uuid",
            "identification",
            "email",
            "alternateEmail",
            //"password",
            "username",
            //"confirmation_code",
            //"remember_token",
            //"confirmed",
            "firstName",
            "lastName",
            "alternateFirstName",
            //"supervisorEmail",
            //"companyUserIdentifier",
            "isSupervisor",
            "isValidator",
            "isActive",
            //"rgt",
            //"lft",
            "hierarchy",
            "defaultLang",
            "notes",
            "level",
            //"notify",
            //"companyId",
            //"syncId",
            //"supervisorId",
            //"externalId",
            //"approverId",
            //"defaultLocationId",
            //"deleted_at",
            //"created_at",
            //"updated_at",
        ];

        $dbFields = array_merge($dbFields, $dbFields_1);

        // 2. Add all the UDLs from the company (from the model):

        $dbFields_2 = [];
        $company = Company::find($companyUserImportJob['company_id']);
        
        $udls = $company->udls;
        $dbFields_2 = [];

        foreach($udls as $val) {
            array_push($dbFields_2, $val->name);
        }

        $dbFields = array_merge($dbFields, $dbFields_2);

        return [
            "id"            => $companyUserImportJob["id"],
            "company_id"    => $companyUserImportJob["company_id"],
            "path"          => $companyUserImportJob["path"],
            "file"          => $companyUserImportJob["file"],
            "totalUsers"    => $companyUserImportJob["total"],
            "createdUsers"  => $companyUserImportJob["created"],
            "updatedUsers"  => $companyUserImportJob["updated"],
            "failedUsers"   => $companyUserImportJob["failed"],
            "CSVfields"     => unserialize($companyUserImportJob["fields"]),
            "DBfields"      => $dbFields,
            "sampleUser"    => unserialize($companyUserImportJob["sample"]),
            "mappings"      => unserialize($companyUserImportJob["mappings"]),
            "status"        => $this->getStatusText($companyUserImportJob["status"]),
            "errors"        => [],
            "created_by_id" => $companyUserImportJob["created_by_id"],
            "updated_by_id" => $companyUserImportJob["updated_by_id"],
            "created_at"    => $companyUserImportJob["created_at"],
            "updated_at"    => $companyUserImportJob["updated_at"]
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

    /**
     * get only required fields for the API, from all the fields
     *
     * @return string
     */
    private function filterUnnecessaryFields($fields) {
        return array_intersect($fields, [
            "uuid",
            "identification",
            "email",
            "alternateEmail",
            //"password",
            "username",
            //"confirmation_code",
            //"remember_token",
            //"confirmed",
            "firstName",
            "lastName",
            "alternateFirstName",
            //"supervisorEmail",
            //"companyUserIdentifier",
            "isSupervisor",
            "isValidator",
            "isActive",
            //"rgt",
            //"lft",
            "hierarchy",
            "defaultLang",
            "notes",
            "level",
            //"notify",
            //"companyId",
            //"syncId",
            //"supervisorId",
            //"externalId",
            //"approverId",
            //"defaultLocationId",
            //"deleted_at",
            //"created_at",
            //"updated_at",
        ]);
    }



}



































