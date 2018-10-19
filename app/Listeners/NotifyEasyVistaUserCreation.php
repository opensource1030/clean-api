<?php

namespace WA\Listeners;

use Illuminate\Support\Facades\Log;
use WA\Events\UserCreated;
use Httpful\Request;

class NotifyEasyVistaUserCreation
{
    public function __construct()
    {
    }

    /**
     * Send a POST to Easyvista
     *
     * @param UserCreated $event
     */
    public function handle(UserCreated $event)
    {
        try {
            $payload = [
                "employees" => [
                    ["EMAIL" => $event->user->email,
                        "IDENTIFICATION" => $event->user->identification,
                        "LAST_NAME" => ${$event->user->firstName} . ', ' . ${$event->user->lastName},
                        "DEPARTMENT_ID" => $event->user->companies()->first()->externalId,
                        "AVAILABLE_FIELD_1" => "Needs Update",
                        "COMMENT_EMPLOYEE" => $event->user->notes
                    ]
                ]
            ];

            $ev_uri = getenv('EV_API_URL') . "/" . getenv('EV_API_ACCOUNT') . '/employees';

            $r = Request::post($ev_uri)
                ->sendsJson()
                ->authenticateWithBasic(getenv('EV_API_LOGIN'), getenv('EV_API_PASSWORD'))
                ->body(json_encode($payload))
                ->send();

            if (!$r->body == 201) {
                Log::info("User added to Easyvista: " . $r->raw_body);
            } else {
                Log::error("Failed adding user to Easyvista: " . $event->user->email);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

    }
}