<?php

namespace WA\Services\Form\Employee;

use WA\Services\Validation\AbstractLaravelValidator;

/**
 * Class EmployeeFormValidator.
 */
class EmployeeFormValidator extends AbstractLaravelValidator
{
    /**
     * Validation rules.
     *
     * @var Array
     */
    protected $rules = [
//    'password' => 'required|min:6',
//    'password_confirmation' => 'required|min:6|same:password',
//    'email' => 'required|email|unique:employees',
        'email' => 'required|email',
        'firstName' => 'required',
        'lastName' => 'required',
//      'supervisorEmail' => 'required',
//    'companyEmployeeIdentifier' => 'required',
//    'username' => 'required',
//    'departmentId' => 'required'
    ];

    /**
     * Custom validation messages.
     *
     * @var Array
     */
    protected $message = [
        'departmentId' => 'A default department is required',
    ];
}
