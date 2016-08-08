<?php

namespace WA\Services\Form\Employee;

use WA\Services\Validation\AbstractLaravelValidator;

/**
 * Class BulkEmployeeFormValidator.
 */
class BulkEmployeeFormValidator extends AbstractLaravelValidator
{
    /**
     * Validation rules.
     *
     * @var Array
     */
    protected $rules = [
        'file' => 'required|mimes:xlsx,csv,xls',
        'companyId' => 'required',
    ];

    /**
     * Custom validation messages.
     *
     * @var Array
     */
    protected $messages = [
        'companyId.required' => 'You must select a company to continue',
        'file.mimes' => 'Please upload a supported file (xls, xlsx or csv)',
        'file.required' => 'You should a file of (xls, xlsx or csv)',
    ];
}
