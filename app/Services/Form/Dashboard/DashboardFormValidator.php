<?php

namespace WA\Services\Form\Dashboard;

use WA\Services\Validation\AbstractLaravelValidator;

/**
 * Class DashboardFormValidator.
 */
class DashboardFormValidator extends AbstractLaravelValidator
{
    protected $rules = [
        'companyId' => 'required',
    ];

    protected $messages = [
        'companyId.required' => 'Please select a company',
    ];
}
