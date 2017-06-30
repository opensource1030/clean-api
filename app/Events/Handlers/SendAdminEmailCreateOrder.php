<?php

/**
 * SendAdminEmailCreateOrder - Gets the event received by the OrderSendEmailEventSubscriber.
 *
 * @author Agustí Dosaiguas
 */
namespace WA\Events\Handlers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use WA\Events\PodcastWasPurchased;

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
     *  @param: $userId = The Id of the User that has set the Order.
     */
    public function sendOrderConfirmationEmail($event) {
        
        try {
            $userOrder = \WA\DataStore\User\User::find($event->order->userId);
            $adminRole = \WA\DataStore\Role\Role::where('name', 'admin')->first();
            $listOfAdmins = \WA\DataStore\User\UserRole::where('role_id', $adminRole->id)->get();

            foreach ($listOfAdmins as $admin) {
                $adminRetrieved = \WA\DataStore\User\User::find($admin->user_id);

                if ($adminRetrieved->companyId == $userOrder->companyId) {
                    $resAdmin = \Illuminate\Support\Facades\Mail::send(
                        'emails.notifications.create_order_admin', // VIEW NAME
                        [
                            'username' => $userOrder->username,
                            'redirectPath' => 'urlderedireccion'
                        ], // PARAMETERS PASSED TO THE VIEW
                        function ($message) {
                            $message->subject('New Order Received.');
                            $message->from(env('MAIL_FROM_ADDRESS'), 'Wireless Analytics');
                            $message->to(env('MAIL_USERNAME'));//$adminRetrieved->email);
                        } // CALLBACK
                    );
                }
            }

            \Log::debug("SendAdminEmailCreateOrder@sendConfirmationEmail - Admin Email has been sent.");
        } catch (\Exception $e) {
            \Log::debug("SendAdminEmailCreateOrder@sendConfirmationEmail - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }
}