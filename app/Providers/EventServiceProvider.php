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
        'WA\Events\SomeEvent' => [
            'WA\Listeners\EventListener',
        ],
        'Aacotroneo\Saml2\Events\Saml2LoginEvent' => [
            'WA\Events\Handlers\Saml2\MainHandler@saml2LoginUser',
        ],
        'Brexis\LaravelWorkflow\Events\GuardEvent' => [
            'WA\Events\Handlers\OrderSendEmailEventSubscriber@onGuard'
        ],
        'Brexis\LaravelWorkflow\Events\LeaveEvent' => [
            'WA\Events\Handlers\OrderSendEmailEventSubscriber@onLeave'
        ],
        'Brexis\LaravelWorkflow\Events\TransitionEvent' => [
            'WA\Events\Handlers\OrderSendEmailEventSubscriber@onTransition'
        ],
        'Brexis\LaravelWorkflow\Events\EnterEvent' => [
            'WA\Events\Handlers\OrderSendEmailEventSubscriber@onEnter'
        ],
        'WA\Events\Handlers\SendUserEmailCreateOrder' => [
            'WA\Events\Handlers\SendUserEmailCreateOrder@sendOrderConfirmationEmail'
        ],
        'WA\Events\Handlers\SendAdminEmailCreateOrder' => [
            'WA\Events\Handlers\SendAdminEmailCreateOrder@sendOrderConfirmationEmail'
        ],
        'WA\Events\Handlers\SendEVEmail' => [
            'WA\Events\Handlers\SendEVEmail@createTicketOnEasyVista'
        ],
        'WA\Events\Handlers\SendUserEmailOrderAccepted' => [
            'WA\Events\Handlers\SendUserEmailOrderAccepted@sendOrderConfirmationEmail'
        ],
        'WA\Events\Handlers\SendUserEmailOrderDenied' => [
            'WA\Events\Handlers\SendUserEmailOrderDenied@sendOrderConfirmationEmail'
        ],
        'WA\Events\Handlers\SendUserEmailOrderDelivered' => [
            'WA\Events\Handlers\SendUserEmailOrderDelivered@sendOrderConfirmationEmail'
        ]
    ];

/*
    public function register(){
        Event::subscribe('WA\Events\Handlers\Saml2\MainHandler');
    }
*/
}
