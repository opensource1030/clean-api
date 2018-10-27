<?php

namespace WA\DataStore\Company;

use WA\DataStore\FilterableTransformer;

/**
 * Class CompanyDomainsTransformer
 * @package WA\DataStore\Company
 */
class CompanyDomainsTransformer extends FilterableTransformer
{
    public function transform(CompanyDomains $domains)
    {
        return [
            'domain'          => $domains->domain,
            'active'             => (int)$domains->active,
            'companyId'           => $domains->companyId,
            'id'                  => (int)$domains->id,
        ];
    }
}
