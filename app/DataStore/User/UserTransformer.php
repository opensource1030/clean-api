<?php

namespace WA\DataStore\User;

use League\Fractal\Resource\Collection as ResourceCollection;
use WA\DataStore\Allocation\AllocationTransformer;
use WA\DataStore\FilterableTransformer;
use WA\DataStore\UdlValue\UdlValueTransformer;

/**
 * Class UserTransformer.
 */
class UserTransformer extends FilterableTransformer
{

    protected $availableIncludes = [
        'assets',
        'devices',
        'companies',
        'roles',
        'allocations',
        'contents',
        'udls',
    ];

    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id'               => $user->id,
            'identification'   => $user->identification,
            'email'            => $user->email,
            'username'         => $user->username,
            'supervisor_email' => $user->supervisorEmail,
            'first_name'       => $user->firstName,
            'last_name'        => $user->lastName,
        ];
    }


    /**
     * @param User $user
     *
     * @return ResourceCollection Allocations
     */
    public function includeAllocations(User $user)
    {
        $this->criteria = $this->getRequestCriteria();
        $allocations = $this->applyCriteria($user->allocations(), $this->criteria, true,
            ['allocations' => 'allocations']);
        $filters = $this->criteria['filters']->get();

        if (in_array('[allocations.billMonth]=[company.currentBillMonth]', $filters)) {
            $allocations->where('billMonth', $user->companies->currentBillMonth);
        }

        return new ResourceCollection($allocations->get(), new AllocationTransformer(), 'allocations');
    }

    /**
     * Dynamic include override because of mixed case
     *
     * @param User $user
     *
     * @return ResourceCollection Contents
     */
    public function includeUdls(User $user)
    {
        $this->criteria = $this->getRequestCriteria();
        $udlValues = $this->applyCriteria($user->udlValues(), $this->criteria);
        return new ResourceCollection($udlValues->get(), new UdlValueTransformer(), 'udlvalues');
    }
}
