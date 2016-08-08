<?php

namespace WA\Auth;

use Auth as IlluminateAuth;

/**
 * Class Auth.
 */
class Auth implements AuthInterface
{
    /**
     * Attempts to login with the given credentials.
     *
     * @param array $input    Array containing the credentials (email/username and password)
     * @param bool  $remember
     *
     * @return bool Success?
     */
    public function login($input, $remember = false)
    {
        if (!isset($input['password'])) {
            $input[ 'password' ] = null;
        }

        $response = IlluminateAuth::attempt(['email' => $input['email'], 'password' => $input['password']], $remember);

        return $response;
    }

    /**
     * Attempts to logout an active employee
     **.
     *
     * @return bool Success?
     */
    public function logout()
    {
        return IlluminateAuth::logout();
    }

    /**
     * Checks if the credentials has been throttled by too
     * much failed login attempts.
     *
     * @param array $input Array containing the credentials (email/username and password)
     *
     * @return bool Is throttled
     */
    public function isThrottled($input)
    {
        return false;
    }

    /**
     * Checks if the given credentials corresponds to a employee that exists but
     * is not confirmed.
     *
     * @param array $input Array containing the credentials (email/username and password)
     *
     * @return bool Exists and is not confirmed?
     */
    public function existsButNotConfirmed($input)
    {
        return IlluminateAuth::attempt([
            'email' => $input['email'],
            'password' => $input['password'],
            'confirmed' => 1,
        ]);
    }

    /**
     * Resets a password of a employee. The $input['token'] will tell which employee.
     *
     * @param array $input Array containing 'token', 'password' and 'password_confirmation' keys.
     *
     * @return bool Success
     */
    public function resetPassword($input)
    {
        //@TODO: implement based on Illuminate
        return false;
    }

    /**
     * Validate if the user is logged in/or not.
     *
     * @return bool
     */
    public function loggedIn()
    {
        return (bool) IlluminateAuth::user();
    }

    /**
     * Get the User if exists
     *
     * @return Object of user
     */
    public function user()
    {
        return IlluminateAuth::user();
    }


}
