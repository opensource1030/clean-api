<?php

namespace WA\Auth;

interface UserAuthInterface
{
    /**
     * Check if the logged in user can add users to the system
     *
     * @return boolean
     */
    public function canAddUsers();

    /**
     * Check if the logged in user can edit other users in the system
     *
     * @return boolean
     */
    public function canEditUsers();

}