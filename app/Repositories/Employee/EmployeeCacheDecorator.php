<?php

namespace WA\Repositories\Employee;

use WA\Services\Cache\CacheInterface;
use WA\Services\Form\Employee\EmployeeForm;

class EmployeeCacheDecorator extends EmployeeDecorator
{
    /**
     * @var \WA\Services\Cache\CacheInterface
     */
    protected $cache;

    /**
     * @param EmployeeInterface $nextEmployee
     * @param CacheInterface    $cache
     */
    public function __construct(EmployeeInterface $nextEmployee, CacheInterface $cache)
    {
        parent::__construct($nextEmployee);

        $this->cache = $cache;
    }

    /**
     * Try to get pagination from cache.
     *
     * @param int  $page
     * @param int  $limit
     * @param bool $all
     *
     * @return mixed
     */
    public function byPage($page = 1, $limit = 10, $all = false)
    {
        $key = md5('page'.$page.'.'.$limit);
//
//        if ($this->cache->has($key)) {
//            return $this->cache->get($key);
//        }

        $paginated = $this->nextEmployee->byPage($page, $limit, $all);

        $this->cache->put($key, $paginated);

        return $paginated;
    }

    /**
     * Try to get first name from cache.
     *
     * @param $firstName
     *
     * @return mixed|object
     */
    public function byFirstName($firstName)
    {
        $key = $this->makeKey('firstName.', $firstName);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->nextEmployee->byFirstName($firstName);

        $this->cache->put($key, $employee);

        return $employee;
    }

    /**
     * Try to retrieve from Cache.
     *
     * @param $lastName
     *
     * @return mixed|object
     */
    public function byLastName($lastName)
    {
        $key = $this->makeKey('lastName', $lastName);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->nextEmployee->byLastName($lastName);
        $this->cache->put($key, $employee);

        return $employee;
    }

    /**
     * Get employee information by supervisor email.
     *
     * @param string $email
     *
     * @return Object of employee information
     */
    public function bySupervisorEmail($email)
    {
        $key = $this->makeKey('supervisorEmail', $email);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->nextEmployee->bySupervisorEmail($email);
        $this->cache->put($key, $employee);

        return $employee;
    }

    /**
     * Get Employee information by the census.
     *
     * @param int $censusId
     * @param int $companyId
     *
     * @return Object of employee information
     */
    public function byCensus($censusId, $companyId)
    {
        $key = $this->makeKey('censusId', $censusId);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->nextEmployee->byCensus($censusId, $companyId);
        $this->cache->put($key, $employee);

        return $employee;
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
        $key = $this->makeKey('employeeEmail', $employeeEmail);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->nextEmployee->byEmail($employeeEmail);
        $this->cache->put($key, $employee);

        return $employee;
    }

    /**
     * Get employee supervisor information by supervisor email.
     *
     * @param string $email of employee
     *
     * @return Object of employee information
     */
    public function getSupervisor($email = null)
    {
        $key = $this->makeKey('employeeEmail', $email);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->nextEmployee->getSupervisor($email);
        $this->cache->put($key, $employee);

        return $employee;
    }

    /**
     * Employees updated by census.
     *
     * @param $censusId
     * @param int  $page
     * @param int  $limit
     * @param bool $all
     *
     * @return mixed
     */
    public function updatedByCensus($censusId, $page = 1, $limit = 10, $all = true)
    {
        $key = md5('updatedbyCensus'.$page.'.'.$limit);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $paginated = $this->nextEmployee->updatedByCensus($censusId, $page, $limit, $all);

        $this->cache->put($key, $paginated);

        return $paginated;
    }

    /**
     * Generate a cache key.
     *
     * @param $slug
     * @param $name
     *
     * @return string
     */
    protected function makeKey(
        $slug,
        $name
    ) {
        return md5($slug.'.'.$name);
    }

    /**
     * Get employees by search term.
     *
     * @param string $query
     * @param int    $page
     * @param int    $limit
     * @param bool   $paginate
     */
    public function bySearch($query, $page = 1, $limit = 10, $paginate = true)
    {
        return $this->nextEmployee->bySearch($query, $page, $limit, $paginate);
    }

    /**
     * Get all the validators (eventually by departments).
     *
     * @param string $companyName
     * @param string $department
     *
     * @return Object object of validator
     */
    public function getValidators($companyName = null, $department = null)
    {
        return $this->nextEmployee->getValidators($companyName, $department);
    }

    /**
     * Get supervisor information by companyId
     *
     * @param int $companyId of company selected
     * @return Array of employee supervisor information
     */
    public function getAllSupervisors($companyId = null)
    {

        if (is_null($companyId)) {
            return null;
        }

        $key = $this->makeKey('supervisors', $companyId);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $supervisors = $this->nextEmployee->getAllSupervisors($companyId);
        $this->cache->put($key, $supervisors);

        return $supervisors;


    }

    /**
     * Get an employee information by API Token
     *
     * @param string $token
     *
     * @return Object of employee information
     */
    public function byToken($token)
    {


        $key = $this->makeKey('apiToken', $token);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->nextEmployee->byToken($token);
        $this->cache->put($key, $employee);

        return $employee;


    }

    /**
     * Get the transformer used by this model
     *
     * @return Object
     */
    public function getTransformer()
    {
        return $this->nextEmployee->getTransformer();
    }

    /**
     * Get employee by name or email
     *
     * @param $name
     * @return Object of employee
     */
    public function byUsernameOrEmail($name){

        $key = $this->makeKey('name', $name);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->nextEmployee->byUsernameOrEmail($name);
        $this->cache->put($key, $employee);

        return $employee;
    }

    /**
     * get the model being used on the object
     *
     * @return mixed
     */
    public function getModel(){
        return $this->nextEmployee->getModel();
    }

    /**
     * Get Udls values for employee
     *
     * @param $employeeId
     * @return Object
     */
    public function getUdls($employeeId){


        $key = $this->makeKey('employeeudls', $employeeId);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->nextEmployee->getUdls($employeeId);
        $this->cache->put($key, $employee);

        return $employee;

    }

    /**
     * Delete an Employees
     *
     * @param int $id
     * @param bool $soft true soft deletes
     * @return bool
     */
    public function delete($id, $soft = true){
        return $this->nextEmployee->delete($id, $soft=true);
    }


    /**
     * Get the list of internal tables that an external system can map to
     *
     * @return array
     */
    public function getMappableFields(){
        return $this->nextEmployee->getMappableFields();
    }

    /**
     * Get validator information by companyId
     *
     * @param int $companyId of company selected
     * @return Array of employee validator information
     */
    public function getAllValidators($companyId = null){
        $key = $this->makeKey('validators', $companyId);

        if ($this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $employee = $this->nextEmployee->getAllValidators($companyId);
        $this->cache->put($key, $employee);

        return $employee;

    }

    /**
     * Delete from the repo by the ID
     *
     * @param int $id
     * @param bool $force completely remove for the DB instead of marking it as "deleted"
     * @return bool of the effect of the creation
     */
    public function deleteById($id, $force = false){
        return $this->nextEmployee->deleteById($id, $force=false);
    }

    /**
     * @param $id
     *
     * @return object Object of employee information
     */
    public function byId($id)
    {
        return $this->nextEmployee->byId($id);
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
     * Check if this employee has asset attached.
     *
     * @param $employeeId
     * @param $assetId
     *
     * @return mixed
     */
    public function hasAsset($employeeId, $assetId)
    {
        return $this->nextEmployee->hasAsset($employeeId, $assetId);
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
        return $this->nextEmployee->hasDevice($employeeId, $deviceId);
    }


    /**
     * @param int $companyIdentifier
     *
     * @return object Object of employee information
     */
    public function byCompanyIdentifier($companyIdentifier)
    {
        return $this->nextEmployee->byCompanyIdentifier($companyIdentifier);
    }

    /**
     * Get employees by their company  id
     *
     * @param int $companyId
     *
     * @return object Object of employees information
     */
    public function byCompanyId($companyId){
        return $this->nextEmployee->byCompanyId($companyId);
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
       return $this->nextEmployee->update($data);
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
        return $this->nextEmployee->syncSupervisor($employeeEmail);
    }

    /**
     * Get an employee by company ID Or Email
     *
     * @param array $data
     *
     * @return mixed
     */
    public function byCompanyIdOrEmail(array $data)
    {
        return $this->nextEmployee->byCompanyIdOrEmail($data);
    }

    /**
     * Get employees by their identification (generated CLEAN ID)
     *
     * @param string $identification
     *
     * @return object Object of employees information
     */
    public function byIdentification($identification)
    {
        return $this->nextEmployee->byIdentification($identification);

    }

    /**
     * Get the maximum value of the external ID.
     *
     * @return int
     */
    public function getMaxExternalId()
    {
        return $this->nextEmployee->getMaxExternalId();
    }

    /**
     * Get the external Id of the employee
     *
     * @param $identifier
     *
     * @return int
     */
    public function getExternalId($identifier)
    {
        return $this->nextEmployee->getExternalId($identifier);
    }

    /**
     * Update External Id based on Identifier
     *
     * @param $identification
     * @param $externalId
     * @return mixed
     */
    public function updateExternalId($identification, $externalId)
    {
        return $this->nextEmployee->updateExternalId($identification, $externalId);
    }

    /**
     * Get identification values of employees with missing external Ids
     * @return array
     */
    public function getMissingExternalIds()
    {
        return $this->nextEmployee->getMissingExternalIds();
    }

    /**
     * Get the Roles assigned to a user by user Id
     *
     * @param int
     * @return mixed
     */
    public function getRoles($id)
    {
        return $this->nextEmployee->getRoles($id);
    }
}
