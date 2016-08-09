<?php

namespace WA\Http\Controllers\User;

use Auth;
use Input;
use Redirect;
use View;
use WA\Auth\UserAuthInterface;
use WA\Http\Controllers\Auth\AuthorizedController;
use WA\Repositories\Location\LocationInterface;
use WA\Services\Form\User\UserForm;

/**
 * Class UserController.
 */
class UserController extends AuthorizedController
{
    /**
     * @var \Dingo\Api\Dispatcher
     */
    protected $api;

    /**
     * @var \WA\Services\Form\User\UserForm
     */
    protected $userForm;

    protected $location;

    protected $userAuth;

    /**
     * @param UserForm      $userForm
     * @param LocationInterface $location
     *
     */
    public function __construct(
        UserForm $userForm,
        LocationInterface $location

    ) {
        $this->userForm = $userForm;
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
            'companyUserIdentifier' => trim(Input::get('companyUserIdentifier')),
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
            'companyUserIdentifier' => trim(Input::get('companyUserIdentifier')),
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
            'departmentId' => $this->userForm->getDepartmentPathId(!empty(Input::get('udls')) ? Input::get('udls') : [], $creatorId, $userInfo),
            'evDepartmentId' => $this->userForm->getDepartmentPathId(!empty(Input::get('udls')) ? Input::get('udls') : [], $creatorId, $userInfo,
                true),
            'user_roles' => Input::get('user_roles'),
        ];

        $this->data = array_merge($data, $this->data);

        $updatedUser = $this->userForm->update($data);

        if (!$updatedUser) {
            $this->data['errors'] = $this->userForm->errors();
            $this->data['employee'] = $this->userForm->show($data['id']);

            return $this->edit($data['id']);
        }

        $this->data['employee'] = $updatedUser;

        return Redirect::route('users.edit', ['id' => $data['id']]);
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
        $user = $this->userForm->edit($id);

        $this->data['employee'] = $user;
        $this->data['locations'] = $this->location->getCountries();
        $this->data['langs'] = $this->location->getUniqueLang();
        $this->data['udls'] = $this->userForm->getUdls();
        $this->data['supervisors'] = $this->userForm->getSupervisors();
        $this->data['langs'] = $this->location->getUniqueLang();
        $this->data['approvers'] = $this->userForm->getValidator();
        $this->data['locations'] = $this->location->getCountries();
        $this->data['currentCompany'] = (bool)$this->userForm->getCurrentCompany();
        $this->data['employeeUdls'] = $this->userForm->getUserUdls($id);

        $udlLoad = $this->userForm->getUdls();
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
            'available_roles' => $this->userForm->getAllRoles(),
            'user_roles' => $this->userForm->getUserRoles($id),


        ]);

        return View::make('users.edit')->with($this->data);
    }

    public function create()
    {
        $bulk = (Input::has('bulk')) ? (bool)Input::get('bulk') : (bool)0;

        if ($bulk) {
            return $this->bulkIndex();
        }

        $udlLoad = $this->userForm->getUdls();

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
                'currentCompany' => $this->userForm->getCurrentCompany(),
                'supervisors' => $this->userForm->getSupervisors(),
                'approvers' => $this->userForm->getValidator(),
                'langs' => $this->location->getUniqueLang(),
                'locations' => $this->location->getCountries(),
                'udlLoad' => $udls,
                'available_roles' => $this->userForm->getAllRoles(),
                'email_domains' => $this->userForm->getEmailDomains($this->currentCompany['id']),

            ]);

        $data = array_merge($data, [
            'maxViewRows' => $maxViewRows,
            'isCensusCompany' => (bool)$this->currentCompany['isCensus'],
        ]);



        return View::make('users.new', $data);
    }

    public function bulkIndex()
    {
        return View::make('users.bulk');
    }

    /**
     * @return mixed
     */
    public function store()
    {
        $supervisorEmail = Input::get('supervisorEmail');

        $userInfo['firstName'] = $data['firstName'] = trim(Input::get('firstName'));
        $userInfo['lastName'] = $data['lastName'] = trim(Input::get('lastName'));
        $userInfo['companyUserIdentifier'] = $data['companyUserIdentifier'] = trim(Input::get('companyUserIdentifier'));
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
        $data['departmentId'] = $this->userForm->getDepartmentPathId($data['udls'], $creatorId, $userInfo);
        $data['evDepartmentId'] = $this->userForm->getDepartmentPathId($data['udls'], $creatorId, $userInfo, true);
        $data['user_roles'] = Input::get('user_roles');


        $this->data = array_merge($data, $this->data);

        if (!$this->userForm->create($data)) {
            $this->data['errors'] = $this->userForm->errors();

            return Redirect::back()
                ->withInput()
                ->withErrors($this->userForm->errors());
        }

        $this->data['employee'] = $this->userForm->getUserByEmail($data['email']);
        $userId = $this->data['employee']['id'];

        return redirect("users/$userId")->with($this->data);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $user = $this->userForm->show($id);
        $department = isset($user->department) ? $user->department : null;
        $permissions = $this->userForm->getUserPermissions($id);
        $user_roles = $this->userForm->getUserRoles($id);

        return View::make('users.show')
            ->with([
                'employee' => $user,
                'pathInHelpDesk' => (isset($department)) ? (bool)$user->department->externalId : false,
                'departmentPath' => (isset($department)) ? $user->department->udlPath : '',
                'helpdesk' => 'EasyVista',
                'permissions' => $permissions,
                'user_roles' => $user_roles,
            ]);
    }

    public function destroy($id)
    {
        if (!$this->userForm->delete($id)) {
            return Redirect::back();
        }

        return $this->index();
    }

    public function index()
    {
        return View::make('users.index');
    }

    /**
     * @param $id
     *
     * Get the users current charges
     */
    public function currentCharges($id)
    {
        $current_charges = $this->userForm->getCurrentCharges($id);
        return $current_charges->toJson();
    }
}
