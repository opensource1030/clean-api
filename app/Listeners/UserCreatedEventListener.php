<?php

namespace WA\Listeners;

use WA\Events\UserCreatedEvent;

class UserCreatedEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param UserCreatedEvent $event
     */
    public function handle(UserCreatedEvent $event)
    {
        $param = new \stdClass();
        $param->employees = [$event->newUser];

        // POST to EASYVISTA
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://wa.easyvista.com/api/v1/50005/employees");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));

        $result = curl_exec($ch);
        curl_close($ch);
    }
}
