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
                    [
                        "E_MAIL" => $event->user->email,
                        "IDENTIFICATION" => $event->user->identification,
                        "LAST_NAME" => $event->user->lastName . ', ' . $event->user->firstName, #. .. , ...
                        "DEPARTMENT_ID" => $event->user->companies()->first()->externalId, # 12
                        "AVAILABLE_FIELD_1" => "Needs Update",
                        "COMMENT_EMPLOYEE" => $event->user->notes,
                        "NOTIFICATION_TYPE_ID" => (string) $event->user->notify
                    ]
                ]
            ];

            $ev_uri = getenv('EV_API_URL') . "/" . getenv('EV_API_ACCOUNT') . '/employees';

            $r = Request::post($ev_uri)
                ->sendsJson()
                ->authenticateWithBasic(getenv('EV_API_LOGIN'), getenv('EV_API_PASSWORD'))
                ->body(json_encode($payload))
                ->send();

            if ($r->code == 201) {
                Log::info("User added to Easyvista: " . $r->raw_body);
            } else {
                Log::error("Failed adding user to Easyvista: " . $event->user->email);
            }
        } catch (\Exception $e) {
            Log::error($e->getFile());
            Log::error($e->getLine());
            Log::error($e->getMessage());
        }

    }
}