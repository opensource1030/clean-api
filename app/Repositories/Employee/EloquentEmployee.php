<?php

namespace WA\Repositories\Employee;

use Illuminate\Database\Eloquent\Model;
use Log;
use DB;
use Webpatser\Uuid\Uuid;
use WA\DataStore\Employee\Employee;
use WA\DataStore\Employee\EmployeeTransformer;
use WA\Repositories\AbstractRepository;
use WA\Repositories\Census\CensusInterface;
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\Udl\UdlInterface;
use WA\Repositories\UdlValue\UdlValueInterface;
use WA\Repositories\UdlValuePath\UdlValuePathInterface;
use WA\Services\Form\Employee\EmployeeForm;
use WA\Services\Form\HelpDesk\EasyVista as ExternalHelpDesk;

class EloquentEmployee extends AbstractRepository implements EmployeeInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * @var \WA\Repositories\Census\CensusInterface
     */
    protected $census;

    /**
     * @var \WA\Repositories\UdlValue\UdlValueInterface
     */
    protected $udlValue;

    /**
     * @var \WA\Repositories\Udl\UdlInterface
     */
    protected $udl;

    /**
     * @var ExternalHelpDesk
     */
    protected $externalHelpDeskService;

    /**
     * @param Model             $model
     * @param CensusInterface   $census
     * @param UdlValueInterface $udlValue
     * @param UdlInterface      $udl
     * @param ExternalHelpDesk  $externalHelpDeskService
     */
    public function __construct(
        Model $model,
        CensusInterface $census,
        UdlValueInterface $udlValue,
        UdlInterface $udl,
        ExternalHelpDesk $externalHelpDeskService
    ) {

        $this->model = $model;
        $this->census = $census;
        $this->udl = $udl;
        $this->udlValue = $udlValue;
//        $this->device = $device;
        $this->externalHelpDeskService = $externalHelpDeskService;

    }

    /**
     * Get paginated census.
     *
     * @param int  $perPage
     * @param bool $paginate
     *
     * @return Object as Collection of object information, | Paginator Collection if pagination is true (default)
     */
    public function byPage($paginate = true, $perPage = 25)
    {
        $model = $this->model;

        if (!$paginate) {
            $ownTable = $model->getTable();

            // manually run the queries
            $response = \DB::table($ownTable)
                ->select(
                    $ownTable . '.id',
                    'firstName',
                    'lastName',
                    'email',
                    'supervisorEmail',
                    'c.name as companyName',
                    'identification'
                )
                ->join('companies as c', 'c.id', '=', $ownTable . '.companyId')
                ->get();

            return $response;
        }

        return $this->model->paginate($perPage);
    }

    /**
     * Get the object by search.
     *
     * @param string $query
     * @param int    $page
     * @param int    $limit
     * @param bool   $paginate
     *
     * @return \StdClass
     */
    public function bySearch($query, $page = 1, $limit = 10, $paginate = true)
    {
        $result = new \StdClass();
        $result->page = $page;
        $result->limit = $limit;
        $result->totalItems = 0;
        $result->items = [];

        $model = $this->model->where('firstName', $query)
            ->orWhere('lastName', $query)
            ->orWhere('email', $query);

        if ($paginate) {
            $model->skip($limit * ($page - 1))
                ->take($limit);

            $result->items = $model->get();
            $result->totalItems = $model->count();
        } else {
            $result->items = $model->get();
            $result->totalItems = $model->count();
        }

        return $result;
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
        return $this->model
            ->where('isActive', 1)
            ->where('lastName', $lastName)
            ->first();
    }

    /**
     * @param int  $id
     * @param bool $active
     *
     * @return Object Object of employee information
     */
    public function byId($id, $active = null)
    {
        $model = $this->model;

        if (!empty($active)) {
            $model = $model->where('isActive', (int)$active);
        }

        $response = null;

        // We want to allow for the passing of  multiple ID (for smarted API)
        if (is_array($id)) {
            if (count($id) == 1) {
                $response = $model->where('id', $id[0])->first();

                return $response;
            } else {
                $response = $model->whereIn('id', $id)
                    ->get();

                return $response;
            }
        }

        $em = $model->where('id', $id)->first();

        return $em;
    }

    /**
     * @param $companyId
     *
     * @return object Object of employee information
     */
    public function byCompanyId($companyId)
    {
        $ownTable = $this->model->getTable();

        // manually run the queries
        $response = \DB::table($ownTable)
            ->select(
                $ownTable . '.id',
                'firstName',
                'lastName', 'email',
                'supervisorEmail',
                'c.name as companyName',
                'identification'
            )
            ->join('companies as c', 'c.id', '=', $ownTable . '.companyId')
            ->where('companyId', $companyId)
            ->where('deleted_at', null)
            ->get();

        return $response;
    }

    /**
     * Check if this employee has asset attached.
     *
     * @param $modelId
     * @param $assetId
     *
     * @return mixed
     */
    public function hasAsset($modelId, $assetId)
    {
        return (bool)
        $this->model->find($modelId)
            ->assets()->find($assetId);
    }

    /**
     * Check if employee has device attached.
     *
     * @param $modelId
     * @param $deviceId
     *
     * @return bool
     */
    public function hasDevice($modelId, $deviceId)
    {
        return (bool)
        $this->model->find($modelId)
            ->asset()->find($deviceId);
    }

    /**
     * Create a new Employee.
     *
     * @param array        $data
     * @param array        $udlValues
     * @param bool         $pushToExternalService |true
     * @param EmployeeForm $employeeForm
     *
     * @return Employee | false
     */
    public function create(
        array $data,
        array $udlValues = [],
        $pushToExternalService = false,
        EmployeeForm $employeeForm = null
    ) {

        $employeeForm = $employeeForm ?: app()->make('WA\Services\Form\Employee\EmployeeForm');
        $tmp_password = null;

        if (isset($data['udlValues'])) {
            $udlValues = $data['udlValues'];
        }

        if (!isset($data['identification']) && !isset($data['companyEmployeeIdentifier'])) {
            $helper = app()->make('WA\Http\Controllers\Admin\HelperController');
            $data['identification'] = $helper->generateIds($data['companyId']);
        }

        if (!isset($data['companyEmployeeIdentifier'])) {
            $data['companyEmployeeIdentifier'] = $data['identification'];
        }

        if (!isset($data['identification']) & isset($data['companyEmployeeIdentifier'])) {
            $data['identification'] = $data['companyEmployeeIdentifier'];
        }

        $data['uuid'] = Uuid::generate(4)->string;
        if (!empty($tmp_email = $data['email'])) {
            $tmp_password = bcrypt('user');
        }

        $tmp_password = $tmp_password ?: bcrypt($data['identification']);

        $employee = $this->byCompanyIdOrEmail($data);

        if ($employee) {
            $this->isUpdatable($employee, $data);

            return true;
        }

        if (empty($udlValues) && (isset($data['udlValues']) && !empty($data['udlValues']))) {
            $udlValues = $this->compactUdlValues($data['udlValues'], $data['companyId']);
        }

        $employeeData = [
            'supervisorEmail' => $sup_email = isset($data['supervisorEmail']) ? strtolower($data['supervisorEmail']) : null,
            'supervisorId' => isset($data['supervisorId']) ? $data['supervisorId'] : null,
            'firstName' => isset($data['firstName']) ? $data['firstName'] : null,
            'alternateFirstName' => isset($data['alternateFirstName']) ? $data['alternateFirstName'] : null,
            'lastName' => isset($data['lastName']) ? $data['lastName'] : null,
            'companyId' => isset($data['companyId']) ? $data['companyId'] : 0,
            'companyExternalId' => isset($data['companyExternalId']) ? $data['companyExternalId'] : $this->getCompanyExternalId($data['companyId']),
            'companyEmployeeIdentifier' => isset($data['companyEmployeeIdentifier']) ?
                $id = $data['companyEmployeeIdentifier'] :
                $id = $data['identification'],
            'isActive' => isset($data['isActive']) ? $data['isActive'] : 0,
            'syncId' => isset($data['syncId']) ? $data['syncId'] : null,
            'defaultLocationId' => isset($data['defaultLocationId']) ? $data['defaultLocationId'] : 236, //US
            'defaultLang' => isset($data['defaultLang']) ? $data['defaultLang'] : 'en',
            'email' => strtolower(isset($data['email']) ? strtolower($data['email']) : null),
            'alternateEmail' => strtolower(isset($data['alternateEmail']) ? strtolower($data['alternateEmail']) : null),
            'password' => $pwd = isset($data['password']) ? bcrypt($data['password']) : $tmp_password,
            'password_confirmation' => $pwd,
            'confirmed' => isset($data['confirmed']) ? $data['confirmed'] : 0,
            'username' => isset($data['username']) ? $data['username'] : null,
            'confirmation_code' => md5(uniqid(mt_rand(), true)),
            'identification' => $data['identification'],
            'notify' => isset($data['notify']) ? $data['notify'] : 0,
            'isSupervisor' => isset($data['isSupervisor']) ? $data['isSupervisor'] : 0,
            'isValidator' => isset($data['isValidator']) ? $data['isValidator'] : 0,
            'notes' => isset($data['notes']) ? $data['notes'] : '',
            'level' => isset($data['level']) ? $data['level'] : '',
            'evDepartmentId' => ($pushToExternalService && isset($data['evDepartmentId'])) ? $data['evDepartmentId'] : '',
            'approverId' => isset($data['approverId']) && isset($data['approverId']) ? $data['approverId'] : 0,
            'externalSupervisorId' => $this->getExternalSupervisorId($sup_email)

        ];

        try {
            $employee = $this->model->create($employeeData);

            if (!$employee && !empty($employee->errors['messages'])) {
                return false;
            }

            /*if (!empty($udlValues) && !is_null($employee->id)) {
                $this->syncUDLValues($employee, $udlValues);
            }*/


            if(!empty($data['user_roles']) && !is_null($employee->id)){
                foreach($data['user_roles'] as $role)
                {
                    $employee->roles()->attach($employee->id, ['role_id' => (int)$role] );
                    $employee->save();
                }
            }else{
                if(!is_null($employee->id))
                {
                    $employee->roles()->attach($employee->id, ['role_id' => 5] );
                    $employee->save();
                }
            }

            //Save Udl Values
            if(!empty($data['udls']) && !is_null($employee->id)){
                foreach($data['udls'] as $udl)
                {
                    foreach ($udl as $udl_id => $udl_value) {

                        $udlValueId = $udl_value['value'];
                    }

                    if (empty($udlValueId)) {
                        continue;
                    }

                    $employee->udlValues()->attach($employee->id,['udlValueId' => (int)$udlValueId]);
                }
            }

            if ($pushToExternalService) {
                if (!$this->externalHelpDeskService->createUser(['input' => $employeeData])) {

                    if (!isset($employeeData['syncId'])) {
                        $employeeForm->notify('error', 'Could not create employee in EasyVista. Try again later');
                    }

                    $this->deleteById($employee->id, true);

                    return false;
                }
            }

            return $employee;

        } catch (\Exception $e) {
            Log::error('[ ' . get_class() . " | " . $e->getLine() . ' ] | There was an issue: ' . $e->getMessage());
        }
    }

    /**
     * @param $companyId
     *
     * @return object Object of employee information
     */
    public function byCompanyIdentifier($companyId)
    {
        $run = $this->model
            ->where('companyEmployeeIdentifier', $companyId)
            ->where('isActive', 1)
            ->first();

        return $run;
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

        return
            $this->model->where('email', $employeeEmail)
                ->first();;
    }

    /**
     * verify if this employee model is updatable.
     *
     * @param Model $employee
     * @param array $data
     *
     * @return bool
     */
    protected function isUpdatable(Model $employee, array $data)
    {
        if ($employee->firstName !== $data['firstName'] ||
            $employee->lastName !== $data['lastName'] ||
            $employee->email !== $data['email'] ||
            $employee->supervisorEmail !== $data['supervisorEmail']
        ) {
            $this->update(array_merge(['id' => $employee->id], $data));
        }

        return true;
    }

    /**
     * Update a new employee.
     *
     * @param array        $data
     * @param array        $udlValues
     * @param bool         $pushToExternalService |true
     * @param EmployeeForm $employeeForm
     *
     * @return bool
     */
    public function update(
        array $data,
        array $udlValues = [],
        $pushToExternalService = true,
        EmployeeForm $employeeForm = null
    ) {
        $employeeForm = $employeeForm ?: app()->make('WA\Services\Form\Employee\EmployeeForm');
        $employeeForm = $employeeForm ?: app()->make('WA\Services\Form\Employee\EmployeeForm');


        if (empty($udlValues) && (isset($data['udlValues']) && !empty($data['udlValues']))) {
            $udlValues = $this->compactUdlValues($data['udlValues'], $data['companyId']);
        }

        $employee = $this->byCompanyIdOrEmail($data);


        if (!$employee) {
            return false;
        }

        $data['identification'] = isset($employee->identification) ? $employee->identification : $data['identification'];
        $data['companyEmployeeIdentifier'] = isset($employee->companyEmployeeIdentifier) ? $employee->companyEmployeeIdentifier : $data['companyEmployeeIdentifier'];

        $data['externalSupervisorId'] = isset($data['supervisorEmail']) ? $this->getExternalSupervisorId($data['supervisorEmail']) : null;

        $employee->email = $data['email'];
        $employee->alternateEmail = strtolower(isset($data['alternateEmail']) ? $data['alternateEmail'] : null);
        $employee->firstName = $data['firstName'];
        $employee->alternateFirstName = isset($data['alternateFirstName']) ? $data['alternateFirstName'] : null;
        $employee->identification = $data['identification'];
        $employee->lastName = $data['lastName'];
        $employee->supervisorEmail = strtolower(isset($data['supervisorEmail']) ? $data['supervisorEmail'] : null);
        $employee->supervisorId = !empty($data['supervisorId']) ? $data['supervisorId'] : null;
        $employee->approverId = isset($data['approverId']) ? $data['approverId'] : null;
        $employee->companyEmployeeIdentifier = $data['companyEmployeeIdentifier'];
        $employee->isActive = isset($data['isActive']) ? $data['isActive'] : 0;
        $employee->isValidator = isset($data['isValidator']) ? $data['isValidator'] : 0;
        $employee->notify = isset($data['notify']) ? $data['notify'] : 0;
        $employee->isSupervisor = isset($data['isSupervisor']) ? $data['isSupervisor'] : 0;
        $employee->defaultLocationId = !empty($data['defaultLocationId']) ? $data['defaultLocationId'] : 8; //US
        $data['defaultLocationId'] = $employee->defaultLocationId;
        $employee->defaultLang = !empty($data['defaultLang']) ? $data['defaultLang'] : 'en';
        $data['defaultLang'] = $employee->defaultLang;

        $employee->departmentId = !empty($data['departmentId']) ? $data['departmentId'] : 0;
        $data['departmentId'] = $employee->departmentId;
        $employee->companyId = !empty($data['companyId']) ? $data['companyId'] : null;
        $employee->notes = isset($data['notes']) ? $data['notes'] : '';
        $data['notes'] = $employee->notes;
        $employee->notify = !empty($data['notify']) ? $data['notify'] : null;
        $data['notify'] = $employee->notify;
        $employee->isValidator = !empty($data['notify']) ? $data['notify'] : null;
        $data['isValidator'] = $employee->isValidator;
        $employee->syncId = isset($data['syncId']) ? $data['syncId'] : $employee->syncId;

        // let's make sure this are properly filled before pushing to external service
        if ($pushToExternalService) {
            $user_info = [
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'companyEmployeeIdentifier' => $data['companyEmployeeIdentifier'],
                'email' => $data['email']
            ];

            if (empty($data['departmentId'])) {
                $data['departmentId'] = $employeeForm->getDepartmentPathId($udlValues, 18, $user_info, false,
                    $data['companyId']); //18 => adminID
            }

            if (empty($data['evDepartmentId'])) {
                $data['evDepartmentId'] = $employeeForm->getDepartmentPathId($udlValues, 18, $user_info, true,
                    $data['companyId']); //18 => adminID
            }

        }

        $data['approverId'] = ($pushToExternalService) ? 0 : 0;

        if (!$employee->save()) {
            return false;
        };

        $this->externalHelpDeskService->updateUser(['input' => $data, 'employee' => $employee]);

       /* if (!empty($udlValues)) {
            $this->syncUDLValues($employee, $udlValues);
        }*/


       //Save User Roles
        $employee->roles()->detach();
        if(!empty($data['user_roles']) && !is_null($employee->id)){
            foreach($data['user_roles'] as $role)
            {
                $employee->roles()->attach($employee->id, ['role_id' => (int)$role] );
                $employee->save();
            }
        }

        //Save Udl Values
        $employee->udlValues()->detach();
        if(!empty($data['udls']) && !is_null($employee->id)){
            foreach($data['udls'] as $udl)
            {
                foreach ($udl as $udl_id => $udl_value) {

                    $udlValueId = $udl_value['value'];
                }

                if (empty($udlValueId)) {
                    continue;
                }

                $employee->udlValues()->attach($employee->id,['udlValueId' => (int)$udlValueId]);
            }
        }

        return $employee;
    }

    /**
     * For every employee, attach their respective UDL values.
     * this expects an array of the key -> value: udl Name = udlValue pairing
     *
     * @param Model $employee
     * @param array $values [(string) udl => (string) udlValue]
     *
     * @return bool
     */
    protected function syncUDLValues(Model $employee, array $values)
    {
        foreach ($values as $udl => $value) {

            $base_udl_value = null;
            $udl_id = null;


            if (!is_array($value)) {

                $udl_id = !is_null($udl_val = $this->udl->byName($udl)) ? $udl_val->id : null;
                $base_udl_value = $value;

            } else {

                foreach ($value as $udl_id_init => $base_udl_value_init) {
                    $udl_id = $udl_id_init;
                    $base_udl_value = $base_udl_value_init['value'];
                }
            }

            if (empty($base_udl_value)) {
                continue;
            }

            $udl_value = $this->udlValue->byNameOrCreate($base_udl_value, $udl_id, $employee['companyId']);

            if (isset($udl_value['id'])) {

                $udlValues[] = $udl_value['id'];

            } else {
                Log::info("The UDL: " . $udl_value . "was not found");
                continue;
            }

        }

        if (!empty($udlValues)) {
            $employee->udlValues()->sync($udlValues);
        }

        return true;
    }

    /**
     * Get Employee information by the census.
     *
     * @param int $censusId
     * @param int $companyId |null
     *
     * @return Object of employee information
     */
    public function byCensus($censusId, $companyId = null)
    {
        return
            $this->model->whereHas('census', function ($q) use ($censusId, $companyId) {
                $q->where('companyId', $companyId)
                    ->where('id', $censusId);
            })->get();

    }

    /**
     * Get employee information by supervisor email.
     *
     * @param string $email
     *
     * @return Object of employee information | null
     */
    public function bySupervisorEmail($email)
    {
        return $this->model->where('supervisorEmail', $email)
            ->where('isActive', 1)
            ->first();
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
        $supervisor = $this->getSupervisor($employeeEmail);
        $employee = $this->byEmail($employeeEmail);

        try {
            if (empty($supervisor)) {
                return false;
            }

            if ($employee->isSelfOrDescendantOf($supervisor)) {
                return false;
            }

            if (!$employee->makeChildOf($supervisor)) {
                return false;
            }

            return true;

        } catch (\Exception $e) {

            Log::error("There was an issue assigning employee:  $employee->email  to $supervisor->email  <<>>" . $e->getMessage());

            return;
        }

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
        if (!is_null($email)) {
            $supervisorEmail = $this->byEmail($email)->supervisorEmail;

            return
                $this->model->where('email', $supervisorEmail)
                    ->where('isActive', 1)
                    ->first();
        }

        return
            $this->model->where('isSupervisor', 1)
                ->groupBy('email')
                ->get();
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
        return $this->model->where('isValidator', 1)
            ->groupBy('email')
            ->get();
    }

    /**
     * Employees updated by census.
     *
     * @param      $censusId
     * @param int  $page
     * @param int  $limit
     * @param bool $paginate
     *
     * @return mixed
     */
    public function updatedByCensus($censusId, $page = 1, $limit = 10, $paginate = true)
    {
        $result = new \StdClass();
        $result->page = $page;
        $result->limit = $limit;
        $result->totalItems = 0;
        $result->items = [];

        $models = $this->model
            ->where('censusId', $censusId)
            ->orderBy('lastName', 'DESC');

        if ($paginate) {
            $models->skip($limit * ($page - 1))
                ->take($limit)
                ->get();
        }

        $result->totalItems = $this->totalEmployees(['censusId', $censusId]);

        $result->items = $models->get();

        return $result;
    }

    protected function totalEmployees(array $whereClause = [])
    {
        $model = $this->model->where('isActive', 1);

        if (!empty($whereClause)) {
            $model->where($whereClause[0], $whereClause[1]);
        }

        return $model->count();
    }

    /**
     * Get the transformer used by this model.
     *
     * @return Object
     */
    public function getTransformer()
    {
        return new EmployeeTransformer();
    }

    /**
     * Get employee by name or email.
     *
     * @param $name
     *
     * @return Object of employee
     */
    public function byUsernameOrEmail($name)
    {
        $model = $this->model;

        $response =
            $model->where('username', $name)
                ->orWhere('email', $name)
                ->first();

        return $response;
    }

    /**
     * Get an employee information by API Token.
     *
     * @param string $token
     *
     * @return Object of employee information
     */
    public function byToken($token)
    {
        return
            $this->model->where('apiToken', $token)
                ->first();
    }

    /**em,p
     * Get Udls values for employeeId
     *
     * @return Object with employee specific UDLs
     */
    public function getUdls($employeeId)
    {
        $udls = $this->model->find($employeeId)->udlValues()->get();

        return $udls;
    }

    /**
     * Delete an Employees.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true)
    {
        if (!$this->model->find($id)) {
            return false;
        }

        if (!$soft) {
            $this->model->forceDelete($id);
        }

        return $this->model->destroy($id);
    }

    /**
     * Get the list of internal tables that an external system can map to.
     *
     * @return array
     */
    public function getMappableFields()
    {
        return [
            'Main Email' => 'email',
            'Alternate Email' => 'alternateEmail',
//            'User Name' => 'username',
            'First Name' => 'firstName',
            'Alternate First Name' => 'alternateFirstName',
            'Last Name' => 'lastName',
            'Supervisor Email' => 'supervisorEmail',
            'Company Identification' => 'companyEmployeeIdentifier',
            'Location' => 'defaultLocationId',
            'Notes' => 'notes',
        ];
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

        if (is_null($companyId)) {
            return null;
        }

        return $this->model->where('companyId', $companyId)
            ->where('isSupervisor', 1)
            ->get()->toArray();
    }

    /**
     * Get validator information by companyId
     *
     * @param int $companyId of company selected
     *
     * @return Array of employee validator information
     */
    public function getAllValidators($companyId = null)
    {

        if (is_null($companyId)) {
            return null;
        }

        return $this->model->where('companyId', $companyId)
            ->where('isValidator', 1)
            ->get()->toArray();
    }

    /**
     * Gets the external Id of a company (uses this as a last resort if the ID is not provided in the load)
     *
     * @param                  $companyId
     * @param CompanyInterface $company
     *
     * @return int of the companyId
     */
    private function getCompanyExternalId($companyId, CompanyInterface $company = null)
    {
        $company = $company ?: app()->make('WA\Repositories\Company\CompanyInterface');

        $company = $company->byId($companyId);

        if (!isset($company)) {
            return null;
        }

        return $company->externalId;
    }

    /**
     * If a supervisor Id is not provided, get it
     *
     * @param $supervisorEmail
     *
     * @return string of supervisor Email | Null
     */
    private function getExternalSupervisorId($supervisorEmail)
    {
        $supervisor = $this->model->where('email', $supervisorEmail)->first();

        if (!isset($supervisor)) {
            return null;
        }

        return $supervisor->externalId;
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
        return
            $this->model->where('identification', $identification)
                ->first();
    }


    /**
     * Compact UDL values into a format suitable to easily gettting the UDL/Value/Paths
     *
     * @param array $udlValues
     * @param int   $companyId
     *
     * @return array
     */
    private function compactUdlValues(array $udlValues, $companyId)
    {
        $compactValues = [];

        foreach ($udlValues as $key => $value) {
            $udlId = $this->udl->byName($key, $companyId)['id'];

            $compactValues[] = [
                $udlId => ['value' => $value]
            ];
        }

        return $compactValues;
    }

    /**
     * Get the departmental name
     *
     * @param                            $externalDepartmentPathId
     * @param UdlValuePathInterface|null $udlValuePath
     *
     * @return mixed
     */
    private function getDepartmentPathName($externalDepartmentPathId, UdlValuePathInterface $udlValuePath = null)
    {
        $udlValuePath = $udlValuePath ?: app()->make('WA\DataStore\UdlValuePath\UdlValuePathInterface');

        return $udlValuePath->byExternalId($externalDepartmentPathId)['udlPath'];
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
        if (!array_key_exists('companyEmployeeIdentifier', $data) && !array_key_exists('email', $data)) {
            return null;
        }

        // we need to check by the two key fields to be sure we don't recreate an existing employee
        $employee = $this->byCompanyIdentifier($data['companyEmployeeIdentifier']);

        if (empty($employee)) {
            $employee = $this->byEmail(strtolower($data['email']));
        }

        return $employee;
    }

    /**
     * Get the maximum value of the external ID.
     *
     * @return int
     */
    public function getMaxExternalId()
    {
        $externalIdColumnName = 'externalId';

        return (int)$this->model->max($externalIdColumnName);
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
        return $this->model->where('identification', $identifier)->pluck('externalId');
    }

    /**
     * Update External Id based on Identifier
     *
     * @param $identification
     * @param $externalId
     *
     * @return mixed
     */
    public function updateExternalId($identification, $externalId)
    {

        return
            $this->model->where('identification', $identification)
                ->update(['externalId' => $externalId]);
    }

    /**
     * Get identification values of employees with missing external Ids
     *
     * @return array
     */
    public function getMissingExternalIds()
    {
        return $this->model
            ->where('externalId', null)
            ->where('companyId', '<>', 9)# Hard exclude WA employees for now.
            ->where('confirmed', 1)
            ->get()->toArray();
    }

    /**
     * Get the Roles assigned to a user by user Id
     *
     * @param int
     *
     * @return mixed
     */
    public function getRoles($id)
    {
        return $this->model->where('id', $id)->first()->roles;
    }
}
