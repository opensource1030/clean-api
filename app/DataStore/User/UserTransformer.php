<?php

namespace WA\DataStore\User;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;
use WA\DataStore\Allocation\AllocationTransformer;
use WA\DataStore\Asset\AssetTransformer;
use WA\DataStore\Company\CompanyTransformer;
use WA\DataStore\Content\ContentTransformer;
use WA\DataStore\Device\DeviceTransformer;
use WA\DataStore\Role\RoleTransformer;
use WA\Helpers\Traits\Criteria;

/**
 * Class UserTransformer.
 */
class UserTransformer extends TransformerAbstract
{
    use Criteria;

    protected $availableIncludes = [
        'assets',
        'devices',
        'companies',
        'roles',
        'allocations',
        'contents',
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

    protected $currentBillMonth = null;

    /**
     * @param User $user
     *
     * @return ResourceItem Company
     */
    public function includeCompanies(User $user)
    {
        return new ResourceItem($user->companies, new CompanyTransformer(), 'companies');
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
    public function includeContents(User $user)
    {
        return new ResourceCollection($user->contents, new ContentTransformer(), 'contents');
    }
}
