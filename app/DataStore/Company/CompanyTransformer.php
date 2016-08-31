<?php

namespace WA\DataStore\Company;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Account\AccountTransformer;
use WA\DataStore\Allocation\AllocationTransformer;
use WA\DataStore\Page\PageTransformer;

/**
 * Class CompanyTransformer.
 */
class CompanyTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'accounts',
        'allocations',
        'pages'
    ];

    /**
     * @param Company $company
     *
     * @return array
     */
    public function transform(Company $company)
    {
        return [
            'id' => (int)$company->id,
            'name' => $company->name,
            'label' => $company->label,
            'active' => $company->active,
            'udlpath' => $company->udlpath,
            'isCensus' => $company->isCensus,
            'udlPathRule' => $company->udlPathRule,
            'assetPath' => $company->assetPath,
        ];
    }


    /**
     * @param Company $company
     *
     * @return ResourceCollection Allocations
     */
    public function includeAllocations(Company $company)
    {
        return new ResourceCollection($company->allocations, new AllocationTransformer(), 'allocations');
    }

    /**
     * @param Company $company
     *
     * @return ResourceCollection Pages
     */
    public function includePages(Company $company)
    {
        return new ResourceCollection($company->pages, new PageTransformer(), 'pages');
    }
}
