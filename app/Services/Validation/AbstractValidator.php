<?php

namespace WA\Services\Validation;

/**
 * Class AbstractValidator.
 */
abstract class AbstractValidator implements ValidableInterface
{
    /**
     * Validator Object.
     *
     * @var object
     */
    protected $validator;

    /**
     * Data to be validated.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Validation Rules that will be worked on.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Validation errors.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Set the data to validate.
     *
     * @param array $data
     *
     * @return self
     */
    public function with(array $data = [])
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Return errors if they exist via the validation.
     *
     * @return array
     */
    public function errors()
    {
        return $this->errors;
    }

    /**
     * Pass the data and set validation rules.
     *
     * @return bool
     */
    abstract public function passes();
}
