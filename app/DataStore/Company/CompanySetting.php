<?php

namespace WA\DataStore\Company;

use WA\DataStore\BaseDataStore;

/**
 * Class Address.
 */
class CompanySetting extends BaseDataStore
{
    protected $table = 'company_settings';

    protected $fillable = [
            'value',
            'name',
            'description',
            'companyId',
            'created_at',
            'updated_at', ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function companies()
    {
        return $this->belongsTo('WA\DataStore\Company\Company', 'companyId');
    }

    /**
     * Get the transformer instance.
     *
     * @return AddressTransformer
     */
    public function getTransformer()
    {
        return new CompanySettingTransformer();
    }
}
