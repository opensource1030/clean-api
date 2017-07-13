<?php

/**
 * SendUserEmailCreateOrder - Gets the event received by the WorkflowEventSubscriber.
 *
 * @author Agustí Dosaiguas
 */
namespace WA\Events\Handlers;

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
     *  @param: $event
     */
    public function sendOrderConfirmationEmail($event) {
        
        try {
            $userOrder = \WA\DataStore\User\User::find($event->order->userId);
            $email = $this->retrieveEmail($userOrder->email);
            $attributes = $this->retrieveTheAttributes($event->order);

            $values['view_name'] = 'emails.notifications.order.order_create_send_user';
            $values['data'] = [
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

                'devicePhoneNo' => '[Mobile Number]',
                
                'deviceCarrier' => isset($attributes['device']['smartphone']['carrier'])
                    ? $attributes['device']['smartphone']['carrier'] : '',
                
                'deviceMake' => isset($attributes['device']['smartphone']['make'])
                    ? $attributes['device']['smartphone']['make'] : '',
                
                'deviceModel' => isset($attributes['device']['smartphone']['model'])
                    ? $attributes['device']['smartphone']['model'] : '',

                'deviceAccessories' => isset($attributes['device']['accessories'])
                    ? $attributes['device']['accessories'] : '',
            ];

            $values['subject'] = 'Order Created.';
            $values['from'] = env('MAIL_FROM_ADDRESS');
            $values['to'] = $email;

            $emailQueue = new \WA\Jobs\EmailQueue($values);
            dispatch($emailQueue);

            \Log::debug("SendUserEmailCreateOrder@sendConfirmationEmail - User Email has been queued.");
        } catch (\Exception $e) {
            \Log::debug("SendUserEmailCreateOrder@sendConfirmationEmail - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }
}