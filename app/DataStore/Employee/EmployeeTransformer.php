<?php

namespace WA\DataStore\Employee;

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
 * Class EmployeeTransformer.
 */
class EmployeeTransformer extends TransformerAbstract
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
     * @param Employee $employee
     *
     * @return array
     */
    public function transform(Employee $employee)
    {
        return [
            'id' => $employee->id,
            'identification' => $employee->identification,
            'email' => $employee->email,
            'username' => $employee->username,
            'supervisor_email' => $employee->supervisorEmail,
            'first_name' => $employee->firstName,
            'last_name' => $employee->lastName,
        ];
    }

    /**
     * @param Employee $employee
     *
     * @return ResourceCollection
     */
    public function includeAssets(Employee $employee)
    {
        return new ResourceCollection($employee->assets, new AssetTransformer(), 'assets');
    }


    /**
     * @param Employee $employee
     *
     * @return ResourceCollection
     */
    public function includeDevices(Employee $employee)
    {
        return new ResourceCollection($employee->devices, new DeviceTransformer(), 'devices');
    }

    /**
     * @param Employee $employee
     *
     * @return ResourceItem Company
     */
    public function includeCompany(Employee $employee)
    {
        return new ResourceItem($employee->company, new CompanyTransformer(), 'company');
    }

    /**
     * @param Employee $employee
     *
     * @return ResourceCollection Roles
     */
    public function includeRoles(Employee $employee)
    {
        return new ResourceCollection($employee->roles, new RoleTransformer(), 'roles');
    }

    /**
     * @param Employee $employee
     *
     * @return ResourceCollection Allocations
     */
    public function includeAllocations(Employee $employee)
    {
        return new ResourceCollection($employee->allocations, new AllocationTransformer(), 'allocations');
    }

    /**
     * @param Employee $employee
     *
     * @return ResourceCollection Pages
     */
    public function includePages(Employee $employee)
    {
        return new ResourceCollection($employee->pages, new PageTransformer(), 'pages');
    }


}
