<?php

namespace WA\Repositories\Employee;

use WA\Services\Form\Employee\EmployeeForm;

abstract class EmployeeDecorator implements EmployeeInterface
{
    protected $nextEmployee;

    public function __construct(EmployeeInterface $nextEmployee)
    {
        $this->nextEmployee = $nextEmployee;
    }

    /**
     * Get paginated employees.
     *
     * @param int $page
     * @param int $limit
     * @param     $all
     *
     * @return mixed
     */
    public function byPage($page = 1, $limit = 10, $all = false)
    {
        $this->nextEmployee->byPage($page, $limit, $all);
    }

    /**
     * Get employee by lastName.
     *
     * @param $lastName
     *
     * @return object Object of employee information
     */
    public function byLastName($lastName)
    {
        $this->nextEmployee->byFirstName($lastName);
    }

    /**
     * Check if this employee has asset attached.
     *
     * @param $employeeId
     * @param $assetId
     *
     * @return mixed
     */
    public function hasAsset($employeeId, $assetId)
    {
        $this->nextEmployee->hasAsset($employeeId, $assetId);
    }

    /**
     * Check if employee has device attached.
     *
     * @param $employeeId
     * @param $deviceId
     *
     * @return bool
     */
    public function hasDevice($employeeId, $deviceId)
    {
        $this->nextEmployee->hasDevice($employeeId, $deviceId);
    }

    /**
     * @param $id
     *
     * @return object Object of employee information
     */
    public function byId($id)
    {
        $this->nextEmployee->byId($id);
    }

    /**
     * @param $companyId
     *
     * @return object Object of employee information
     */
    public function byCompanyIdentifier($companyId)
    {
        $this->nextEmployee->byCompanyIdentifier($companyId);
    }


    /**
     * Create a new Employee.
     *
     * @param array        $data
     * @param array        $udlValues
     * @param bool         $pushToExternalService |true
     * @param EmployeeForm $employeeForm
     *
     * @return Object object of the employee | false
     */
    public function create(
        array $data,
        array $udlValues = [],
        $pushToExternalService = true,
        EmployeeForm $employeeForm = null
    ) {
        $employeeForm = $employeeForm ?: app()->make('WA\Services\Form\Employee\EmployeeForm');

        return $this->nextEmployee->create($data, $udlValues, $pushToExternalService, $employeeForm);
    }

    /**
     * Update a new employee.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $this->nextEmployee->update($data);
    }

    /**
     * Update an employee's supervisor.
     *
     * @param $employeeEmail
     *
     * @return bool
     */
    public function syncSupervisor($employeeEmail)
    {
        $this->nextEmployee->syncSupervisor($employeeEmail);
    }

    /**
     * @param $firstName
     *
     * @return object Object of employee information
     */
    public function byFirstName($firstName)
    {
        $this->nextEmployee->byFirstName($firstName);
    }

    /**
     * Get supervisor information by companyId
     *
     * @param int $companyId of company selected
     *
     * @return Array of employee supervisor information
     */
    public function getAllSupervisors($companyId = null)
    {

        $this->nextEmployee->getAllSupervisors($companyId);
    }

    /**
     * Get an employee information by email.
     *
     * @param string $employeeEmail
     *
     * @return Object of employee information
     */
    public function byEmail($employeeEmail)
    {
        $this->nextEmployee->byEmail($employeeEmail);
    }
}
