<?php

namespace WA\Events;

class UserCreatedEvent extends Event
{
    public $newUser;

    /**
     * Create a new event instance.
     */
    public function __construct($user)
    {
        $newUser = new \stdClass();
        $newUser->E_MAIL = $user->email;
        $newUser->IDENTIFICATION = $user->identification;
        $newUser->LAST_NAME = $user->firstName . ',' .$user->lastName;
//        $newUser->
        $newUser->AVAILABLE_FIELD_1 = "Needs Update";
    }
}