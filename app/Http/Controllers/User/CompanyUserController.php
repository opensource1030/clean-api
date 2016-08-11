<?php
/**
 * Created by PhpStorm.
 * User: domotosho
 * Date: 2/24/16
 * Time: 11:22 AM
 */

namespace WA\Http\Controllers\User;


use Illuminate\Http\Request;
use WA\Http\Controllers\BaseController;
use WA\Services\Form\User\CompanyUserForm;

/**
 * Class CompanyUserController
 *
 * @package WA\Http\Controllers\User
 */
class CompanyUserController extends BaseController
{
    /**
     * @var CompanyUserForm
     */
    protected $userForm;

    /**
     * CompanyUserController constructor.
     *
     * @param CompanyUserForm $userForm
     */
    public function __construct(
        CompanyUserForm $userForm
    ) {
        $this->userForm = $userForm;
    }

    public function store(Request $request)
    {
        $helper = app()->make('WA\Http\Controllers\Admin\HelperController');

        $data['email'] = trim($request->input('email'));
        $companyId = $this->userForm->getCompanyIdByEmail($data['email']);

        if (!(bool)$companyId) {
            $this->userForm->notify('error',
                'The email address you have entered is not a valid domain for your organization. Please enter your company-standard email address and try again.');
            /**
             *   Session employee_exists is used to control the login flow.
             *   This will make the New registration form to be shown.
             */
            return redirect()->back()
                ->with(['company_new' => true])
                ->withInput();
        }

        $data['isLegacy'] = $request->input('legacy');
        $data['legacyDestination'] = $request->input('legacyDestination');

        $company = $this->userForm->getCompany($companyId);
        $password = $helper->randGenerator('', 7, '');

        $data['companyId'] = $companyId;
        $data['companyExternalId'] = $company->externalId;
        $data['firstName'] = !empty($first_name = $request->input('firstName'))
            ? $first_name
            : $company->name;
        $data['lastName'] = !empty($last_name = $request->input('lastName'))
            ? $last_name
            : 'Pending';
        $data['username'] = !empty($username = $request->input('username'))
            ? $username
            : explode('@', $data['email'])[0];
        $data['password'] = !empty($request->input('password'))
            ? $request->input('password')
            : $password;
        $data['password_confirmation'] = $request->input('password_confirmation')
            ? $request->input('password')
            : $password;
        $data['confirmed'] = 1;
        $data['notify'] = 0;
        $data['udls'] = !empty($request->input('udls')) ? $request->input('udls') : [];

        $data['departmentId'] = $this->userForm->getDepartmentPathId($companyId);
        $data['evDepartmentId'] = $this->userForm->getDepartmentPathId($companyId, true);

        $this->data = array_merge($data, $this->data);

        if (!$this->userForm->create($data)) {
            $this->data['errors'] = $this->userForm->errors();

            return redirect()->back()
                ->with(['company_new' => true])
                ->withInput()
                ->withErrors($this->userForm->errors());
        }

        $user = $this->userForm->getUserByEmail($data['email']);
        $this->userForm->sendLoginDetails($user, [
            'password' => $password,
            'isLegacy' => $data['isLegacy'],
            'legacyDestination' => $data['legacyDestination']
        ]);

        if ((bool)$data['isLegacy']) {
            return view('auth.redirect_new_legacy',['givenEmail' => $data['email']]);
        }

        return redirect()->route('login');
    }


}