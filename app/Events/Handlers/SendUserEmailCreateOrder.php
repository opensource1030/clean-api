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
     *  @param: $event
     */
    public function sendOrderConfirmationEmail($event) {

        try {
            $userOrder = \WA\DataStore\User\User::find($event->order->userId);
            $email = $this->retrieveEmail($userOrder->email);
            $attributes = $this->retrieveTheAttributes($event->order);
            \Log::debug("ATTRIBUTES". print_r($attributes, true));
            $values['view_name'] = 'emails.notifications.order.order_create_send_user';
            $values['data'] = [
                'orderType' => $attributes['order']['orderType'],
                'username' => $attributes['user']['username'],
                'userEmail' => $attributes['user']['userEmail'],
                'supervisorEmail' => $attributes['user']['supervisorEmail'],
                'activeUser' => $attributes['user']['activeUser'],
                'udlDepartment' => $attributes['user']['udlDepartment'],
                'udlCostCenter' => $attributes['user']['udlCostCenter'],
                'packageName' => $attributes['package']['packageName'],
                'approvalCode' => $attributes['package']['approvalCode'],
                'serviceImei' => $attributes['service']['serviceImei'],
                'servicePhoneNo' => $attributes['service']['servicePhoneNo'],
                'serviceSim' => $attributes['service']['serviceSim'],
                'deviceImei' => $attributes['device']['deviceImei'],
                'deviceCarrier' => $attributes['device']['deviceCarrier'],
                'deviceSim' => $attributes['device']['deviceSim'],
                'domesticvoice' => $attributes['service']['domesticvoice'],
                'domesticdata' => $attributes['service']['domesticdata'],
                'domesticmessage' => $attributes['service']['domesticmessage'],
                'internationalvoice' => $attributes['service']['internationalvoice'],
                'internationaldata' => $attributes['service']['internationaldata'],
                'internationalmessage' => $attributes['service']['internationalmessage'],
                'devicePhoneNo' => $attributes['device']['devicePhoneNo'],
                'deviceMake' => $attributes['device']['deviceMake'],
                'deviceModel' => $attributes['device']['deviceModel'],
                'deviceAccessories' => $attributes['device']['deviceAccessories'],
                'companyName' => $attributes['company']['companyName'],
                'addressAddress' => $attributes['address']['addressAddress'],
                'addressCity' => $attributes['address']['addressCity'],
                'addressState' => $attributes['address']['addressState'],
                'addressPostalCode' => $attributes['address']['addressPostalCode'],
                'showCurrentService' => $attributes['if']['showCurrentService'],
                'showCurrentDevice' => $attributes['if']['showCurrentDevice'],
                'showNewService' => $attributes['if']['showNewService'],
                'showNewDevice' => $attributes['if']['showNewDevice'],
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