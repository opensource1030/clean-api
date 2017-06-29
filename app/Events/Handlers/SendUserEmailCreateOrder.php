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
                'emails.notifications.new_order_created', // VIEW NAME
                [
                    'username' => $userOrder->username,
                    'redirectPath' => 'urlderedireccion',
                    'username' => isset($attributes['user']['username']) 
                        ? $attributes['user']['username'] : '',
                    'useremail' => isset($attributes['user']['email']) 
                        ? $attributes['user']['email'] : '',
                    'usersupervisoremail' => isset($attributes['user']['supervisorEmail']) 
                        ? $attributes['user']['supervisorEmail'] : '',
                    'addressname' => isset($attributes['address']['name']) 
                        ? $attributes['address']['name'] : '',
                    'addresscity' => isset($attributes['address']['city']) 
                        ? $attributes['address']['city'] : '',
                    'addressstate' => isset($attributes['address']['state']) 
                        ? $attributes['address']['state'] : '',
                    'addresscountry' => isset($attributes['address']['country']) 
                        ? $attributes['address']['country'] : '',
                    'packagename' => isset($attributes['package']['name']) 
                        ? $attributes['package']['name'] : '',
                    'servicetitle' => isset($attributes['service']['title']) 
                        ? $attributes['service']['title'] : '',
                    'serviceitemsdomvo' => isset($attributes['service']['domesticvoice']) 
                        ? $attributes['service']['domesticvoice'] : '',
                    'serviceitemsdomdata' => isset($attributes['service']['domesticdata']) 
                        ? $attributes['service']['domesticdata'] : '',
                    'serviceitemsdommess' => isset($attributes['service']['domesticmess']) 
                        ? $attributes['service']['domesticmess'] : '',
                    'serviceitemsintvo' => isset($attributes['service']['internationalvoice']) 
                        ? $attributes['service']['internationalvoice'] : '',
                    'serviceitemsintdata' => isset($attributes['service']['internationaldata']) 
                        ? $attributes['service']['internationaldata'] : '',
                    'serviceitemsintmess' => isset($attributes['service']['internationalmess']) 
                        ? $attributes['service']['internationalmess'] : '',
                    'deviceinfo' => isset($attributes['device']['deviceInfo']) 
                        ? $attributes['device']['deviceInfo'] : '',
                ], // PARAMETERS PASSED TO THE VIEW
                function ($message) use ($userOrder) {
                    $message->subject('New Order Created.');
                    $message->from(env('MAIL_FROM_ADDRESS'), 'Wireless Analytics');
                    $message->to('didac.pallares@siriondev.com');//$userOrder->email);
                } // CALLBACK
            );
            \Log::debug("SendUserEmailCreateOrder@sendConfirmationEmail - User Email has been sent.");
        } catch (\Exception $e) {
            \Log::debug("SendUserEmailCreateOrder@sendConfirmationEmail - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }
}