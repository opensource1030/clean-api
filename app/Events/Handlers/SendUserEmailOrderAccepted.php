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
class SendUserEmailOrderAccepted extends \WA\Events\Handlers\BaseHandler
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
        $events->listen('sendOrderConfirmationEmail', 'WA\Events\Handlers\SendUserEmailOrderAccepted@sendOrderConfirmationEmail');
    }

    /**
     *  @param: $userId = The Id of the User that has set the Order.
     */
    public function sendOrderConfirmationEmail($event) {
        
        try {
            $userOrder = \WA\DataStore\User\User::find($event->order->userId);
            $email = if(isset(env('MAIL_USERNAME'))) : env('MAIL_USERNAME') ? $userOrder->email;

            $values['view_name'] = 'emails.notifications.order.order_accept_send_user';
            $values['data'] = [];
            $values['subject'] = 'Order Accepted';
            $values['from'] = env('MAIL_FROM_ADDRESS');
            $values['to'] = $email;

            $emailQueue = new \WA\Jobs\EmailQueue($values);
            dispatch($emailQueue);

            \Log::debug("SendUserEmailOrderAccepted@sendConfirmationEmail - User Order Accepted Email has been sent.");
        } catch (\Exception $e) {
            \Log::debug("SendUserEmailOrderAccepted@sendConfirmationEmail - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }
}