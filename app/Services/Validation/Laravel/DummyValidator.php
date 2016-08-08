<?php

namespace WA\Services\Validation\Laravel;

use WA\Services\Validation\ValidableInterface;

/**
 * Class DummyValidator.
 */
class DummyValidator extends LaravelValidator implements ValidableInterface
{
    /**
     *  A Dummy Validation for testing, nothing more --
     *  will do for covering the basis for every other basic validator.
     *
     * @var array
     */
    protected $rules = [
        'email' => 'required|min:5',
        'firstName' => 'required|alpha_dash',
        'username' => 'required',
    ];
}
