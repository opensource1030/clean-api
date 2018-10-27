<?php

namespace WA\DataStore\Company;

use WA\DataStore\BaseDataStore;

/**
 * * WA\DataStore\Company\CompanyDomains.
 *
 * @mixin \Eloquent
 */
class CompanyDomains extends BaseDataStore
{
    protected $table = 'company_domains';

    protected $fillable = ['domain', 'active', 'companyId'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function companies()
    {
        return $this->belongsTo('WA\DataStore\Company\Company', 'companyId');
    }

    /**
     * @return CompanyDomainsTransformer
     */
    public function getTransformer()
    {
        return new CompanyDomainsTransformer();
    }
}
