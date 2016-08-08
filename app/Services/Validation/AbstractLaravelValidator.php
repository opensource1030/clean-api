<?php

namespace WA\Services\Validation;

use Illuminate\Validation\Factory as Validator;

/**
 * Class AbstractLaravelValidator.
 */
abstract class AbstractLaravelValidator implements ValidableInterface
{
    protected $validator;

    /**
     * Validation data key => value.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Validation errors.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * The Validation rules.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Custom validation messages.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function with(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Check if the validation passes.
     *
     * @return bool true if this passes
     */
    public function passes()
    {
        $validator = $this->validator->make(
            $this->data,
            $this->rules,
            $this->messages
        );

        if ($validator->fails()) {
            $this->errors = $validator->messages();

            return false;
        }

        return true;
    }

    /**
     * If there is error, return it.
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }
}
