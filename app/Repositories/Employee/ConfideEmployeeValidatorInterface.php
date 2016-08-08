<?php

namespace WA\Repositories\Employee;

interface ConfideEmployeeValidatorInterface
{
    /**
     * Validates the given user. Should check if all the fields are correctly
     * and may check other stuff too, like if the user is unique.
     *
     * @param ConfideEmployeeInterface $user Instance to be tested.
     *
     * @return bool True if the $user is valid.
     */
    public function validate(ConfideEmployeeInterface $user);
}
