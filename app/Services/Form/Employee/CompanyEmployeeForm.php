<?php

namespace WA\Services\Form\Employee;

use Illuminate\Session\SessionManager as Session;
use Log;
use Mail;
use WA\DataStore\Company\Company;
use WA\DataStore\Employee\Employee;
use WA\Helpers\Traits\SetLimits;
use WA\Http\Controllers\Admin\HelperController;
use WA\Repositories\Company\CompanyInterface;
use WA\Repositories\Employee\EmployeeInterface;
use WA\Repositories\HelpDesk\HelpDeskInterface;
use WA\Repositories\Udl\UdlInterface;
use WA\Services\Form\AbstractForm;
use WA\Services\Soap\HelpDeskEasyVista as HelpDeskApi;

/**
 * Class EmployeeForm.
 */
class CompanyEmployeeForm extends AbstractForm
{
    /**
     * @var EmployeeFormValidator
     */
    protected $validator;

    /**
     * @var \WA\Repositories\Employee\EmployeeInterface
     */
    protected $employee;

    /**
     * @var HelpDeskApi
     */
    protected $helpDeskApi;

    /**
     * @var HelpDeskInterface
     */
    protected $helpDesk;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var CompanyInterface
     */
    protected $company;


    public function __construct(
        EmployeeInterface $employee,
        CompanyEmployeeFormValidator $validator,
        CompanyInterface $company

    ) {
        $this->validator = $validator;
        $this->employee = $employee;
        $this->company = $company;
    }

    /**
     * @param array $input
     *
     * @return bool|Object of employee data
     */
    public function create(array $input)
    {
        if (!$this->isValidCompanyDomain($input['email'])) {
            $this->notify('error',
                'The email address you have entered is not a valid domain for your organization. Please enter your company-standard email address and try again.');
            return false;
        }

        $email = isset($input['email']) ? $input['email'] : null;
        if(!empty($email))
        {
            $employee = $this->employee->byEmail($email);
            if(!empty($employee))
            {
                $this->notify('error', 'There is an existing employee with the given email. Please try another email address.');
                return false;
            }
        }


        $employee = $this->employee->create($input, []);

        if (!$employee) {
            $this->notify('error', 'Something strange happened, could not created Employee. try again later');

            return false;
        };

        if (!$employee) {
            $this->notify('error', 'There was an issue creating this employee');

            return false;
        }

        $this->notify('success',
            'Account Successfully created, please check your email and try to login');

        return true;
    }

    /**
     * Gets the company ID by the email
     *
     * @param string $email
     *
     * @return int
     */
    public function getCompanyIdByEmail($email)
    {
        return $this->company->getIdByEmployeeEmail($email);
    }

    /**
     * Check if this is a valid company domain
     *
     * @param string $email
     *
     * @return bool
     */
    protected function isValidCompanyDomain($email)
    {
        $companyId = $this->company->getIdByEmployeeEmail($email);

        return (bool)$companyId;
    }

    /**
     * Send the confirmation email for login info
     *
     * @param Employee $employee
     * @param array    $optional []
     *
     * @return bool
     */
    public function sendLoginDetails(Employee $employee, $optional = [])
    {
        $redirect_base = route('login');
        $redirectPath = ((bool)$optional['legacyDestination']) ? $optional['legacyDestination'] : $redirect_base;
//        $appName = ((bool)$optional['isLegacy']) ? "CLEAN Platform" : "CLEAN Platform";
        $appName = "Wireless Analytics";

        $data = [
            'email' => $employee->email,
            'password' => isset($optional['password']) ? $optional['password'] : "",
            'companyName' => ucfirst($this->company->byId($employee->companyId)->name),
            'redirectPath' => $redirectPath,
            'appName' => $appName
        ];

        $mail = Mail::send('emails.employee.welcome', $data, function ($m) use ($employee, $appName) {
            $m->from(env('MAIL_FROM_ADDRESS'), 'Wireless Analytics');
            $m->to($employee->email)->subject('Welcome to ' . $appName . ' !');
        });

        return true;
    }

    /**
     * Get an Employee by Email.
     *
     * @param $email
     *
     * @return Object of Employee
     */
    public function getEmployeeByEmail($email)
    {
        $employee = $this->employee->byEmail($email);

        return $employee;
    }

    /**
     * Get the company by it's id
     *
     * @param int $id
     *
     * @return Company
     */
    public function getCompany($id)
    {
        return $this->company->byId($id);
    }

    /**
     * Returns the path ID given the udl path.
     *
     * @param int  $companyId  of the company
     * @param bool $externalId should be return or not
     *
     * @return int of the department path ID
     */
    public function getDepartmentPathId(
        $companyId,
        $externalId = false
    ) {
        $path = $this->company->getUdlValuePathId($companyId, [], $externalId, null, []);

        return $path;
    }
}
