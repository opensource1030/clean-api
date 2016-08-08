<?php

namespace WA\Services\Validation\Laravel;

use WA\Services\Validation\ValidableInterface;

/**
 * Class EmployeeValidator.
 */
class EmployeeValidator extends LaravelValidator implements ValidableInterface
{
    /**
     * Validation for creating new Employee.
     */
    protected $rules = [
        'email' => 'required|email|unique:employees',
        'firstName' => 'required|alpha_dash',
        'lastName' => 'required',
//        'companyEmployeeIdentifier' => 'required',
    ];
}
