<?php

namespace WA\Services\Validation\Laravel;

use WA\Services\Validation\ValidableInterface;

/**
 * Class UserValidator.
 */
class UserValidator extends LaravelValidator implements ValidableInterface
{
    /**
     * Validation for creating new User.
     */
    protected $rules = [
        'email' => 'required|email|unique:users',
        'firstName' => 'required|alpha_dash',
        'lastName' => 'required',
//        'companyUserIdentifier' => 'required',
    ];
}
