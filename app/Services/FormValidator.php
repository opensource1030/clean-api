<?php

namespace WA\Services;

use WA\Services\Validation\AbstractLaravelValidator;

/**
 * Class FormValidator.
 */
class FormValidator extends AbstractLaravelValidator
{
    protected $rules = [
        'email' => 'required',
        'password' => 'required',
    ];
}
