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

    protected $defaultIncludes = [
        'address'
    ];

    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id'                        => $user->id,
            'uuid'                      => $user->uuid,
            'identification'            => $user->identification,
            'email'                     => $user->email,
            'alternateEmail'            => $user->alternateEmail,
            'password'                  => $user->password,
            'username'                  => $user->username,
            'confirmation_code'         => $user->confirmation_code,
            'remember_token'            => $user->remember_token,
            'confirmed'                 => $user->confirmed,
            'firstName'                 => $user->firstName,
            'lastName'                  => $user->lastName,
            'alternateFirstName'        => $user->alternateFirstName,
            'supervisorEmail'           => $user->supervisorEmail,
            'companyUserIdentifier'     => $user->companyUserIdentifier,
            'isSupervisor'              => $user->isSupervisor,
            'isValidator'               => $user->isValidator,
            'isActive'                  => $user->isActive,
            'rgt'                       => $user->rgt,
            'lft'                       => $user->lft,
            'hierarchy'                 => $user->hierarchy,
            'defaultLang'               => $user->defaultLang,
            'notes'                     => $user->notes,
            'level'                     => $user->level,
            'notify'                    => $user->notify,
            'companyId'                 => $user->companyId,
            'syncId'                    => $user->syncId,
            'supervisorId'              => $user->supervisorId,
            'externalId'                => $user->externalId,
            'approverId'                => $user->approverId,
            'defaultLocationId'         => $user->defaultLocationId,
            'addressId'                 => $user->addressId,
            'deleted_at'                => $user->deleted_at,
            'created_at'                => $user->created_at,
            'updated_at'                => $user->updated_at
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
