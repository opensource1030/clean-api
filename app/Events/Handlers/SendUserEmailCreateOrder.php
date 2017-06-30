<?php

/**
 * SendUserEmailCreateOrder - Gets the event received by the OrderSendEmailEventSubscriber.
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
class SendUserEmailCreateOrder extends \WA\Events\Handlers\BaseHandler
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
        $events->listen('sendOrderConfirmationEmail', 'WA\Events\Handlers\SendUserEmailCreateOrder@sendOrderConfirmationEmail');
    }

    /**
     *  @param: $userId = The Id of the User that has set the Order.
     */
    public function sendOrderConfirmationEmail($event) {
        
        try {
            $userOrder = \WA\DataStore\User\User::find($event->order->userId);

            $attributes = $this->retrieveTheAttributes($event->order);

            $resUser = \Illuminate\Support\Facades\Mail::send(
                'emails.notifications.create_order_user', // VIEW NAME
                [
                    'username' => $userOrder->username,

                    'userEmail' => isset($attributes['user']['email'])
                        ? $attributes['user']['email'] : '',

                    'supervisorEmail' => isset($attributes['user']['supervisorEmail'])
                        ? $attributes['user']['supervisorEmail'] : '',

                    'orderId' => $event->order->id,

                    'addressName' => isset($attributes['address']['name'])
                        ? $attributes['address']['name'] : '',

                    'addressAddress' => isset($attributes['address']['address'])
                        ? $attributes['address']['address'] : '',

                    'addressCity' => isset($attributes['address']['city'])
                        ? $attributes['address']['city'] : '',

                    'addressCountry' => isset($attributes['address']['country'])
                        ? $attributes['address']['country'] : '',

                    'addressPostalCode' => isset($attributes['address']['postalCode'])
                        ? $attributes['address']['postalCode'] : '',

                    'serviceDescription' => isset($attributes['service']['description'])
                        ? $attributes['service']['description'] : '',

                    'domesticvoice' => isset($attributes['service']['domesticvoice'])
                        ? $attributes['service']['domesticvoice'] : '',

                    'domesticdata' => isset($attributes['service']['domesticdata'])
                        ? $attributes['service']['domesticdata'] : '',

                    'domesticmessage' => isset($attributes['service']['domesticmess'])
                        ? $attributes['service']['domesticmess'] : '',

                    'internationalvoice' => isset($attributes['service']['internationalvoice'])
                        ? $attributes['service']['internationalvoice'] : '',

                    'internationaldata' => isset($attributes['service']['internationaldata'])
                        ? $attributes['service']['internationaldata'] : '',

                    'internationalmessage' => isset($attributes['service']['internationalmess'])
                        ? $attributes['service']['internationalmess'] : '',

                ], // PARAMETERS PASSED TO THE VIEW
                function ($message) use ($userOrder) {
                    $message->subject('New Order Created.');
                    $message->from(env('MAIL_FROM_ADDRESS'), 'Wireless Analytics');
                    $message->to(env('MAIL_USERNAME'));//$userOrder->email);
                } // CALLBACK
            );
            \Log::debug("SendUserEmailCreateOrder@sendConfirmationEmail - User Email has been sent.");
        } catch (\Exception $e) {
            \Log::debug("SendUserEmailCreateOrder@sendConfirmationEmail - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }
}