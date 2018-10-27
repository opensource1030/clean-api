<?php

namespace WA\Events;


use Illuminate\Queue\SerializesModels;
use WA\DataStore\User\User;
use Illuminate\Database\Eloquent\Model;

class UserCreated
{
    use SerializesModels;

    /**
     * @var User
     */
    public $user;

    /**
     * Create  a new user event instance
     *
     * UserCreated constructor.
     * @param User $user
     */
    public function __construct(Model $user)
    {
        $this->user = $user;
    }
}