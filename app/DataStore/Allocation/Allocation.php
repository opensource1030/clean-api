<?php


namespace WA\DataStore\Allocation;

use WA\DataStore\Allocation\AllocationTransformer;
use WA\DataStore\BaseDataStore;


/**
 * Class Allocations
 *
 * @package WA\DataStore
 * @property-read \WA\DataStore\Company\Company $companies
 * @property-read \WA\DataStore\User\User $users
 * @mixin \Eloquent
 */
class Allocation extends BaseDataStore
{
    protected $table = 'allocations';

    public $timestamps = false;


    public function companies()
    {
        return $this->belongsTo('WA\DataStore\Company\Company', 'companyId');
    }

    public function users()
    {
        return $this->belongsTo('WA\DataStore\User\User', 'employeeId');
    }

    /**
     * Get the transformer instance.
     *
     * @return mixed
     */
    public function getTransformer()
    {
        return new AllocationTransformer();
    }


}