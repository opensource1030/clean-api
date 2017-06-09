<?php

namespace WA\DataStore\Company;

use WA\DataStore\BaseDataStore;
use Illuminate\Support\Facades\Auth;

/**
 * Class CompanyUserImportJob.
 *
 * @mixin \Eloquent
 */
class CompanyUserImportJob extends BaseDataStore
{
    protected $table = 'company_user_import_jobs';

    protected $fillable = [
        'company_id',
        'path',
        'file',
        'total',
        'created',
        'updated',
        'failed',
        'fields',
        'sample',
        'mappings',
        'status',
        'created_at',
        'created_by_id',
        'updated_at',
        'updated_by_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function companies()
    {
        return $this->belongsTo('WA\DataStore\Company\Company', 'company_id');
    }

    /**
     * Get the transformer instance.
     *
     * @return CompanyUserImportJobTransformer
     */
    public function getTransformer()
    {
        return new CompanyUserImportJobTransformer();
    }

}
