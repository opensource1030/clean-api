<?php

namespace WA\Repositories\User;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * Interface that declares the methods that must be
 * present in the User method that is going to be used
 * with Confide. (modified to use Laravel's Contracts).
 *
 * If you are looking for a implementation for this
 * methods see ConfideUser trait.
 *
 * @see \Zizaco\Confide\ConfideUser
 *
 * @license MIT
 */
interface ConfideUserInterface extends AuthenticatableContract, CanResetPasswordContract
{
    /**
     * Confirm the user (usually means that the user) email is valid.
     *
     * @return bool
     */
    public function confirm();

    /**
     * Send email with information about password reset.
     *
     * @return string
     */
    public function forgotPassword();

    /**
     * Checks if the current user is valid.
     *
     * @return bool
     */
    public function isValid();
}
