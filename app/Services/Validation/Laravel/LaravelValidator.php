<?php

namespace WA\Services\Validation\Laravel;

use Illuminate\Validation\Factory;
use WA\Services\Validation\AbstractValidator;

/**
 * Class LaravelValidator.
 */
abstract class LaravelValidator extends AbstractValidator
{
    /**
     * Laravel's validator.
     *
     * @var \Illuminate\Validation\Factory
     */
    protected $validator;

    /**
     * @param Factory $validator
     */
    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @return bool
     */
    public function passes()
    {
        $validator = $this->validator->make($this->data, $this->rules);

        if ($validator->fails()) {
            $this->errors = $validator->messages();

            return false;
        }

        return true;
    }
}
