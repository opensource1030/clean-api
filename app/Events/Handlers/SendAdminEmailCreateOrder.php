<?php

/**
 * SendAdminEmailCreateOrder - Gets the event received by the WorkflowEventSubscriber.
 *
 * @author Agustí Dosaiguas
 */
namespace WA\Events\Handlers;

/**
 * Class SendAdminEmailCreateOrder.
 */
class SendAdminEmailCreateOrder extends \WA\Events\Handlers\BaseHandler
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
        $events->listen('sendOrderConfirmationEmail', 'WA\Events\Handlers\SendAdminEmailCreateOrder@sendOrderConfirmationEmail');
    }

    /**
     *  @param: $event
     */
    public function sendOrderConfirmationEmail($event) {
        
        try {
            $userOrder = \WA\DataStore\User\User::find($event->order->userId);
            $adminRole = \WA\DataStore\Role\Role::where('name', 'admin')->first();
            $listOfAdmins = \WA\DataStore\User\UserRole::where('role_id', $adminRole->id)->get();

            foreach ($listOfAdmins as $admin) {
                $adminRetrieved = \WA\DataStore\User\User::find($admin->user_id);

                if ($adminRetrieved->companyId == $userOrder->companyId) {

                    $values['view_name'] = 'emails.notifications.order.order_create_send_admin';
                    $values['data'] = [
                        'username' => $userOrder->username,
                        'redirectPath' => 'urlderedireccion'
                    ];
                    $values['subject'] = 'New Order Received.';
                    $values['from'] = env('MAIL_FROM_ADDRESS');
                    $values['to'] = /*env('MAIL_USERNAME'); //*/ $adminRetrieved->email;

                    $emailQueue = new \WA\Jobs\EmailQueue($values);
                    dispatch($emailQueue);
                }
            }

            \Log::debug("SendAdminEmailCreateOrder@sendConfirmationEmail - Admin Email has been queued.");
        } catch (\Exception $e) {
            \Log::debug("SendAdminEmailCreateOrder@sendConfirmationEmail - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }
}