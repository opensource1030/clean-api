<?php

namespace WA\DataStore\User;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Allocation\AllocationTransformer;
use WA\DataStore\Asset\AssetTransformer;
use WA\DataStore\Company\CompanyTransformer;
use WA\DataStore\Device\DeviceTransformer;
use WA\DataStore\Page\PageTransformer;
use WA\DataStore\Role\Role;
use WA\DataStore\Role\RoleTransformer;

/**
 * Class UserTransformer.
 */
class UserTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'assets',
        'devices',
        'company',
        'roles',
        'allocations',
        'pages'
    ];

    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'identification' => $user->identification,
            'email' => $user->email,
            'username' => $user->username,
            'supervisor_email' => $user->supervisorEmail,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
        ];
    }

    /**
     * @param User $user
     *
     * @return ResourceCollection
     */
    public function includeAssets(User $user)
    {
        return new ResourceCollection($user->assets, new AssetTransformer(), 'assets');
    }


    /**
     * @param User $user
     *
     * @return ResourceCollection
     */
    public function includeDevices(User $user)
    {
        return new ResourceCollection($user->devices, new DeviceTransformer(), 'devices');
    }

    /**
     * @param User $user
     *
     * @return ResourceItem Company
     */
    public function includeCompany(User $user)
    {
        return new ResourceItem($user->company, new CompanyTransformer(), 'company');
    }

    /**
     * @param User $user
     *
     * @return ResourceCollection Roles
     */
    public function includeRoles(User $user)
    {
        return new ResourceCollection($user->roles, new RoleTransformer(), 'roles');
    }

    /**
     * @param User $user
     *
     * @return ResourceCollection Allocations
     */
    public function includeAllocations(User $user)
    {
        return new ResourceCollection($user->allocations, new AllocationTransformer(), 'allocations');
    }

    /**
     * @param User $user
     *
     * @return ResourceCollection Pages
     */
    public function includePages(User $user)
    {
        return new ResourceCollection($user->pages, new PageTransformer(), 'pages');
    }


}
