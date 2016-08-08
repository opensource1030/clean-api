<?php

namespace WA\Services\Validation;

/**
 * Interface ValidableInterface.
 */
interface ValidableInterface
{
    /**
     * Data that validation will be run against.
     *
     * @param array $input
     *
     * @return ValidableInterface
     */
    public function with(array $input);

    /**
     * Check if the validation passes.
     *
     * @return bool
     */
    public function passes();

    /**
     * Validation Errors.
     *
     * @return array
     */
    public function errors();
}
