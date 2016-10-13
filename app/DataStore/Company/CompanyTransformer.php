<?php

namespace WA\DataStore\Company;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Allocation\AllocationTransformer;
use WA\DataStore\Content\ContentTransformer;
use WA\Helpers\Traits\Criteria;

/**
 * Class CompanyTransformer.
 */
class CompanyTransformer extends TransformerAbstract
{

    use Criteria;

    protected $availableIncludes = [
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
            'id'               => (int)$company->id,
            'name'             => $company->name,
            'label'            => $company->label,
            'active'           => $company->active,
            'udlpath'          => $company->udlpath,
            'isCensus'         => $company->isCensus,
            'udlPathRule'      => $company->udlPathRule,
            'assetPath'        => $company->assetPath,
            'currentBillMonth' => $company->currentBillMonth,
        ];
    }


    /**
     * @param Company $company
     *
     * @return ResourceCollection Allocations
     */
    public function includeAllocations(Company $company)
    {
        $allocations = $this->applyCriteria($company->allocations(), $this->criteria);
        $filters = $this->criteria['filters']->get();

        if (in_array("[allocations.billMonth]=[company.currentBillMonth]", $filters)) {
            $allocations->where('billMonth', $company->currentBillMonth);
        }
        return new ResourceCollection($company->allocations, new AllocationTransformer(), 'allocations');
    }

    /**
     * @param Company $company
     *
     * @return ResourceCollection Contents
     */
    public function includeContents(Company $company)
    {
        $contents = $company->contents;

        if(count($contents) < 1)
        {
            //Return the default contents
            $contentInterface = app()->make('WA\Repositories\Content\ContentInterface');
            $defaultContents = $contentInterface->getDefaultContent();
            $contents = !empty($defaultContents) ? $defaultContents : $contents ;
        }

        return new ResourceCollection($contents, new ContentTransformer(), 'contents');
    }
}
