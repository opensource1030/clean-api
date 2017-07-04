<?php

/**
 * SendUserEmailCreateOrder - Gets the event received by the WorkflowEventSubscriber.
 *
 * @author Agustí Dosaiguas
 */
namespace WA\Events\Handlers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use WA\Events\PodcastWasPurchased;

/**
 * Class SendUserEmailCreateOrder.
 */
class SendUserEmailOrderDelivered extends \WA\Events\Handlers\BaseHandler
{
    protected $order;

    /**
     * Create a new event instance.
     */
    public function __construct(\WA\DataStore\Order\Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param Dispatcher $events
     */
    public function handle(Dispatcher $events)
    {
        $events->listen('sendOrderConfirmationEmail', 'WA\Events\Handlers\SendUserEmailOrderDelivered@sendOrderConfirmationEmail');
    }

    /**
     *  @param: $userId = The Id of the User that has set the Order.
     */
    public function sendOrderConfirmationEmail($event) {
        
        try {
            $userOrder = \WA\DataStore\User\User::find($event->order->userId);

            /*
            $resUser = \Illuminate\Support\Facades\Mail::send(
                'emails.notifications.create_order_user', // VIEW NAME
                [
                    'username' => $userOrder->username,

                ], // PARAMETERS PASSED TO THE VIEW
                function ($message) use ($userOrder) {
                    $message->subject('New Order Created.');
                    $message->from(env('MAIL_FROM_ADDRESS'), 'Wireless Analytics');
                    $message->to(env('MAIL_USERNAME'));//$userOrder->email);
                } // CALLBACK
            );
            */
            \Log::debug("SendUserEmailOrderDelivered@sendConfirmationEmail - User Order Delivered Email has been sent.");
        } catch (\Exception $e) {
            \Log::debug("SendUserEmailOrderDelivered@sendConfirmationEmail - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }
}