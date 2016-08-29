<?php

namespace WA\DataStore\Company;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Account\AccountTransformer;
use WA\DataStore\Allocation\AllocationTransformer;
use WA\DataStore\Content\ContentTransformer;

/**
 * Class CompanyTransformer.
 */
class CompanyTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'accounts',
        'allocations',
        'contents'
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
     * @return ResourceCollection Contents
     */
    public function includeContents(Company $company)
    {
        return new ResourceCollection($company->contents, new ContentTransformer(), 'contents');
    }
}
