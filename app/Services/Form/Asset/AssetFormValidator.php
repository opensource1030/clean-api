<?php

namespace WA\Services\Form\Asset;

use WA\Services\Validation\AbstractLaravelValidator;

/**
 * Class AssetFormValidator.
 */
class AssetFormValidator extends AbstractLaravelValidator
{
    protected $rules = [
        'employeeId' => 'required',
        'alias' => 'required|alpha_dash',
    ];

    protected $messages = [
        'email.required' => 'You must select someone to assign to',
    ];
}
