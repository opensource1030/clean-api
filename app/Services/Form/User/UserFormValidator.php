<?php

namespace WA\Services\Form\User;

use WA\Services\Validation\AbstractLaravelValidator;

/**
 * Class UserFormValidator.
 */
class UserFormValidator extends AbstractLaravelValidator
{
    /**
     * Validation rules.
     *
     * @var Array
     */
    protected $rules = [
//    'password' => 'required|min:6',
//    'password_confirmation' => 'required|min:6|same:password',
//    'email' => 'required|email|unique:users',
        'email' => 'required|email',
        'firstName' => 'required',
        'lastName' => 'required',
//      'supervisorEmail' => 'required',
//    'companyUserIdentifier' => 'required',
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
