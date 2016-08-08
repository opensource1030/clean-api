<?php

namespace WA\Services\Form\Employee;

use WA\Services\Validation\AbstractLaravelValidator;

/**
 * Class EmployeeFormValidator.
 */
class CompanyEmployeeFormValidator extends AbstractLaravelValidator
{
    /**
     * Validation rules.
     *
     * @var array
     */
    protected $rules = [
        'email' => 'required|email',
    ];
    
    // verify that the email is in of the acceptable domains
}
