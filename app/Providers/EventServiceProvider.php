<?php

namespace WA\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;
use Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Aacotroneo\Saml2\Events\Saml2LoginEvent' => [
            'WA\Events\Handlers\Saml2\MainHandler@saml2LoginUser',
        ],
        'Brexis\LaravelWorkflow\Events\GuardEvent' => [
            'WA\Events\Handlers\WorkflowEventSubscriber@onGuard'
        ],
        'Brexis\LaravelWorkflow\Events\LeaveEvent' => [
            'WA\Events\Handlers\WorkflowEventSubscriber@onLeave'
        ],
        'Brexis\LaravelWorkflow\Events\TransitionEvent' => [
            'WA\Events\Handlers\WorkflowEventSubscriber@onTransition'
        ],
        'Brexis\LaravelWorkflow\Events\EnterEvent' => [
            'WA\Events\Handlers\WorkflowEventSubscriber@onEnter'
        ],
        'WA\Events\OrderCreateTransition' => [
            'WA\Events\Handlers\SendEVRequest@createTicketOnEasyVista',
            'WA\Events\Handlers\SendUserEmailCreateOrder@sendOrderConfirmationEmail',
            //'WA\Events\Handlers\SendAdminEmailCreateOrder@sendOrderConfirmationEmail'
        ],
        'WA\Events\OrderAcceptedTransition' => [
            'WA\Events\Handlers\SendUserEmailOrderAccepted@sendOrderConfirmationEmail'
        ],
        'WA\Events\OrderDeniedTransition' => [
            'WA\Events\Handlers\SendUserEmailOrderDenied@sendOrderConfirmationEmail'
        ],
        'WA\Events\OrderDeliveredTransition' => [
            'WA\Events\Handlers\SendUserEmailOrderDelivered@sendOrderConfirmationEmail'
        ],
//        'WA\Events\UserCreatedEvent' => [
//            'WA\Listeners\UserCreatedEventListener'
//        ],
        'WA\Events\UserCreated' => [
            'WA\Listeners\NotifyEasyVistaUserCreation'
        ],
    ];
}
