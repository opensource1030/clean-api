<?php

namespace WA\Http\Controllers\Employee;

use Auth;
use Input;
use Redirect;
use View;
use WA\Auth\EmployeeAuthInterface;
use WA\Http\Controllers\Auth\AuthorizedController;
use WA\Repositories\Location\LocationInterface;
use WA\Services\Form\Employee\EmployeeForm;

/**
 * Class EmployeeController.
 */
class EmployeeController extends AuthorizedController
{
    /**
     * @var \Dingo\Api\Dispatcher
     */
    protected $api;

    /**
     * @var \WA\Services\Form\Employee\EmployeeForm
     */
    protected $employeeForm;

    protected $location;

    protected $employeeAuth;

    /**
     * @param EmployeeForm      $employeeForm
     * @param LocationInterface $location
     *
     */
    public function __construct(
        EmployeeForm $employeeForm,
        LocationInterface $location

    ) {
        $this->employeeForm = $employeeForm;
        $this->location = $location;

        parent::__construct();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function update($id)
    {
        $creatorId = $this->user['id'];
        $userInfo = [
            'firstName' => trim(Input::get('firstName')),
            'lastName' => trim(Input::get('lastName')),
            'email' => trim(Input::get('email')),
            'companyEmployeeIdentifier' => trim(Input::get('companyEmployeeIdentifier')),
        ];



        $data = [
            'id' => $id,
            'firstName' => trim(Input::get('firstName')),
            'lastName' => trim(Input::get('lastName')),
            'email' => trim(Input::get('email')),
            'approverId' => (int)Input::get('approverId'),
            'companyId' => Input::get('companyId'),
            'supervisorEmail' => !empty(Input::get('supervisorEmail')) ? explode('__',
                Input::get('supervisorEmail'))[0] : null,
            'supervisorId' => !empty(Input::get('supervisorEmail')) ? explode('__',
                Input::get('supervisorEmail'))[1] : null,
            'companyEmployeeIdentifier' => trim(Input::get('companyEmployeeIdentifier')),
            'identificationRegenerate' => (bool)(Input::get('identificationRegenerate') === "on" ? 1 : 0),
            'isActive' => (int)Input::get('isActive'),
            'isValidator' => (int)Input::get('isValidator'),
            'notify' => !empty(Input::get('notify')) ? (int)((bool)Input::get('notify')) : 5,
            'isSupervisor' => (int)((bool)Input::get('isSupervisor')),
            'censusId' => (int)Input::get('censusId'),
            'defaultLocationId' => !empty(Input::get('defaultLocationId')) ? (int)Input::get('defaultLocationId') : 8,
            'defaultLang' => Input::get('defaultLang'),
            'level' => (int)Input::get('level'),
            'notes' => Input::get('notes'),
            'udls' => Input::get('udls'),
            'isCensusCompany' => Input::get('isCensusCompany'),
            'departmentId' => $this->employeeForm->getDepartmentPathId(!empty(Input::get('udls')) ? Input::get('udls') : [], $creatorId, $userInfo),
            'evDepartmentId' => $this->employeeForm->getDepartmentPathId(!empty(Input::get('udls')) ? Input::get('udls') : [], $creatorId, $userInfo,
                true),
            'user_roles' => Input::get('user_roles'),
        ];

        $this->data = array_merge($data, $this->data);

        $updatedEmployee = $this->employeeForm->update($data);

        if (!$updatedEmployee) {
            $this->data['errors'] = $this->employeeForm->errors();
            $this->data['employee'] = $this->employeeForm->show($data['id']);

            return $this->edit($data['id']);
        }

        $this->data['employee'] = $updatedEmployee;

        return Redirect::route('employees.edit', ['id' => $data['id']]);
    }

    /**
     * @param $id
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function edit($id)
    {
        $employee = $this->employeeForm->edit($id);

        $this->data['employee'] = $employee;
        $this->data['locations'] = $this->location->getCountries();
        $this->data['langs'] = $this->location->getUniqueLang();
        $this->data['udls'] = $this->employeeForm->getUdls();
        $this->data['supervisors'] = $this->employeeForm->getSupervisors();
        $this->data['langs'] = $this->location->getUniqueLang();
        $this->data['approvers'] = $this->employeeForm->getValidator();
        $this->data['locations'] = $this->location->getCountries();
        $this->data['currentCompany'] = (bool)$this->employeeForm->getCurrentCompany();
        $this->data['employeeUdls'] = $this->employeeForm->getEmployeeUdls($id);

        $udlLoad = $this->employeeForm->getUdls();
        $udlCount = $totalCount = count($udlLoad);

        $udls = [];
        $maxViewRows = 4;
        $perRowCount = 0;

        foreach ($udlLoad as $key => $index) {
            $udls[$perRowCount][$key] = $index;

            if ($udlCount % $maxViewRows == 0) {
                $perRowCount += 1;
            }

            $udlCount -= 1;
        }

        $this->data = array_merge($this->data, [
            'maxViewRows' => $maxViewRows,
            'isCensusCompany' => (bool)$this->currentCompany['isCensus'],
            'currentCompany' => $this->currentCompany,
            'udlLoad' => $udls,
            'available_roles' => $this->employeeForm->getAllRoles(),
            'user_roles' => $this->employeeForm->getUserRoles($id),


        ]);

        return View::make('employees.edit')->with($this->data);
    }

    public function create()
    {
        $bulk = (Input::has('bulk')) ? (bool)Input::get('bulk') : (bool)0;

        if ($bulk) {
            return $this->bulkIndex();
        }

        $udlLoad = $this->employeeForm->getUdls();

        $udls = [];
        $udlCount = $totalCount = count($udlLoad);
        $maxViewRows = 4;
        $perRowCount = 0;

        foreach ($udlLoad as $key => $index) {
            $udls[$perRowCount][$key] = $index;

            if ($udlCount % $maxViewRows == 0) {
                $perRowCount += 1;
            }

            $udlCount -= 1;
        }


        $data = array_merge(
            $this->data,
            [
                'currentCompany' => $this->employeeForm->getCurrentCompany(),
                'supervisors' => $this->employeeForm->getSupervisors(),
                'approvers' => $this->employeeForm->getValidator(),
                'langs' => $this->location->getUniqueLang(),
                'locations' => $this->location->getCountries(),
                'udlLoad' => $udls,
                'available_roles' => $this->employeeForm->getAllRoles(),
                'email_domains' => $this->employeeForm->getEmailDomains($this->currentCompany['id']),

            ]);

        $data = array_merge($data, [
            'maxViewRows' => $maxViewRows,
            'isCensusCompany' => (bool)$this->currentCompany['isCensus'],
        ]);



        return View::make('employees.new', $data);
    }

    public function bulkIndex()
    {
        return View::make('employees.bulk');
    }

    /**
     * @return mixed
     */
    public function store()
    {
        $supervisorEmail = Input::get('supervisorEmail');

        $userInfo['firstName'] = $data['firstName'] = trim(Input::get('firstName'));
        $userInfo['lastName'] = $data['lastName'] = trim(Input::get('lastName'));
        $userInfo['companyEmployeeIdentifier'] = $data['companyEmployeeIdentifier'] = trim(Input::get('companyEmployeeIdentifier'));
        $data['companyId'] = Input::get('companyId');
        $data['companyExternalId'] = Input::get('companyExternalId');
        $data['supervisorEmail'] = (!empty($supervisorEmail)) ? explode('__', Input::get('supervisorEmail'))[0] : null;
        $data['supervisorId'] = (!empty($supervisorEmail)) ? explode('__', Input::get('supervisorEmail'))[1] : null;
        $data['approverId'] = Input::get('approverId');

        $userEmail = trim(Input::get('email')).'@'. Input::get('domain');
        $userInfo['email'] = $data['email'] = $userEmail;

        $data['username'] = !empty(Input::get('username')) ? Input::get('username') : null;
        $data['password'] = !empty(Input::get('password')) ? Input::get('password') : strtolower(explode('@',
            $data['email'])[0]);
        $data['password_confirmation'] = Input::get('password_confirmation') ? Input::get('password') : strtolower(explode('@',
            $data['email'])[0]);
        $data['confirmed'] = (bool)Input::get('confirmed');
        $data['defaultLocationId'] = (int)Input::get('defaultLocationId');
        $data['defaultLang'] = Input::get('lang');
        $data['notify'] = (int)((bool)Input::get('notify'));
        $data['isSupervisor'] = (int)((bool)Input::get('isSupervisor'));
        $data['isValidator'] = (int)((bool)Input::get('isValidator'));
        $data['notes'] = Input::get('notes');
        $data['level'] = Input::get('level');
        $data['isCensusCompany'] = (bool)Input::get('isCensusCompany');
        $data['udls'] = !empty(Input::get('udls')) ? Input::get('udls') : [];
        $creatorId = $this->user['id'];
        $data['departmentId'] = $this->employeeForm->getDepartmentPathId($data['udls'], $creatorId, $userInfo);
        $data['evDepartmentId'] = $this->employeeForm->getDepartmentPathId($data['udls'], $creatorId, $userInfo, true);
        $data['user_roles'] = Input::get('user_roles');


        $this->data = array_merge($data, $this->data);

        if (!$this->employeeForm->create($data)) {
            $this->data['errors'] = $this->employeeForm->errors();

            return Redirect::back()
                ->withInput()
                ->withErrors($this->employeeForm->errors());
        }

        $this->data['employee'] = $this->employeeForm->getEmployeeByEmail($data['email']);
        $employeeId = $this->data['employee']['id'];

        return redirect("employees/$employeeId")->with($this->data);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $employee = $this->employeeForm->show($id);
        $department = isset($employee->department) ? $employee->department : null;
        $permissions = $this->employeeForm->getUserPermissions($id);
        $user_roles = $this->employeeForm->getUserRoles($id);

        return View::make('employees.show')
            ->with([
                'employee' => $employee,
                'pathInHelpDesk' => (isset($department)) ? (bool)$employee->department->externalId : false,
                'departmentPath' => (isset($department)) ? $employee->department->udlPath : '',
                'helpdesk' => 'EasyVista',
                'permissions' => $permissions,
                'user_roles' => $user_roles,
            ]);
    }

    public function destroy($id)
    {
        if (!$this->employeeForm->delete($id)) {
            return Redirect::back();
        }

        return $this->index();
    }

    public function index()
    {
        return View::make('employees.index');
    }

    /**
     * @param $id
     *
     * Get the employees current charges
     */
    public function currentCharges($id)
    {
        $current_charges = $this->employeeForm->getCurrentCharges($id);
        return $current_charges->toJson();
    }
}
