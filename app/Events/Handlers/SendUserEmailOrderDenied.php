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
class SendUserEmailOrderDenied extends \WA\Events\Handlers\BaseHandler
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
     *  @param: $userId = The Id of the User that has set the Order.
     */
    public function sendOrderConfirmationEmail($event) {
        
        try {
            $userOrder = \WA\DataStore\User\User::find($event->order->userId);
            $email = $this->retrieveEmail($userOrder->email);

            $values['view_name'] = 'emails.notifications.order.order_deny_send_user';
            $values['data'] = [
                'urlGif' => \URL::asset('assets/img/animat-noplugin-256x256-color.gif'),
                'redirectPath' => env('FRONTEND_DOMAIN', '*') . '/dashboard'
            ];
            $values['subject'] = 'Order Denied.';
            $values['from'] = env('MAIL_FROM_ADDRESS');
            $values['to'] = $email;

            $emailQueue = new \WA\Jobs\EmailQueue($values);
            dispatch($emailQueue);

            \Log::debug("SendUserEmailOrderDenied@sendConfirmationEmail - User Order Denied Email has been sent.");
        } catch (\Exception $e) {
            \Log::debug("SendUserEmailOrderDenied@sendConfirmationEmail - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }
}