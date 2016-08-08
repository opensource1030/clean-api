<?php

namespace WA\Auth;

interface CDIAuthInterface
{
    /**
     * Check if the logged in user can access CDI related functions
     *
     * @return boolean
     */
    public function getCDIAuthorization();
}


