<?php

namespace WA\Services\Form\Login;

use WA\Services\Validation\AbstractLaravelValidator;

/**
 * Class LoginFormValidator.
 */
class LoginFormValidator extends AbstractLaravelValidator
{
    protected $rules = [
        'email' => 'required',
        'password' => 'required',
    ];
}
