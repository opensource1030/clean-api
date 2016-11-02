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
        $allocations = $this->applyCriteria($user->allocations(), $this->criteria);
        $filters = $this->criteria['filters']->get();

        if (in_array('[allocations.billMonth]=[company.currentBillMonth]', $filters)) {
            $allocations->where('billMonth', $user->companies->currentBillMonth);
        }

        return new ResourceCollection($allocations->get(), new AllocationTransformer(), 'allocations');
    }

    /**
     * @param User $user
     *
     * @return ResourceCollection Contents
     */
    public function includeUdls(User $user)
    {
        return new ResourceCollection($user->udlValues, new UdlValueTransformer(), 'udlvalues');
    }
}
