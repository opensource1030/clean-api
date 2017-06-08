<?php

namespace WA\DataStore\Company;

use WA\DataStore\FilterableTransformer;

/**
 * Class CompanySettingTransformer.
 */
class CompanySettingTransformer extends FilterableTransformer
{
    protected $availableIncludes = [
        'companies'
    ];

    /**
     * @param CompanySetting $companySetting
     *
     * @return array
     */
    public function transform(CompanySetting $companySetting)
    {
        return [
            'id'            => (int)$companySetting->id,
            'value'         => $companySetting->value,
            'name'          => $companySetting->name,
            'description'   => $companySetting->description,
            'companyId'     => (int)$companySetting->companyId,
            'created_at'    => $companySetting->created_at,
            'updated_at'    => $companySetting->updated_at,
        ];
    }
}
