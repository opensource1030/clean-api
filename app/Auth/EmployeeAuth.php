<?php

namespace WA\Auth;

use Auth;
use Illuminate\Support\Facades\Session;


class EmployeeAuth implements \WA\Auth\EmployeeAuthInterface
{
    protected $user;

    protected $currentCompany;

    public function __construct()
    {
        $this->currentCompany = Session::get('clean.company');
        $this->user = Auth::user();
    }

    /**
     * Check if the logged in user can add users to the system
     *
     * @return boolean
     */
    public function canAddUsers()
    {
        return $this->user->can('add_users');
    }

    /**
     * Check if the logged in user can edit other users in the system
     *
     * @return boolean
     */
    public function canEditUsers()
    {
        return $this->user->can('edit_users');
    }
}