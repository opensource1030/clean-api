<?php

/**
 * SendEVEmail - Gets the event received by the OrderSendEmailEventSubscriber.
 *
 * @author   Agustí Dosaiguas
 */

namespace WA\Events\Handlers;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use WA\Events\PodcastWasPurchased;

/**
 * Class MainHandler.
 */
class SendEVEmail extends \WA\Events\Handlers\BaseHandler
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
        $events->listen('createTicketOnEasyVista', 'WA\Events\Handlers\SendEVEmail@createTicketOnEasyVista');
    }

    public function createTicketOnEasyVista($event) {
        
        try {
            $attributes = $this->easyVistaStringDescription($event->order);
            
            $post_data = array(
                'Catalog_GUID' => '',
                'Catalog_Code' => $attributes['packageAC'],
                'AssetID' => '',
                'AssetTag' => '',
                'ASSET_NAME' => '',
                'Urgency_ID' => '2',
                'Severity_ID' => '41',
                'External_reference' => '',
                'Phone' => '',
                'Requestor_Identification' => '',
                'Requestor_Mail' => $attributes['email'],
                'Requestor_Name' => '',
                'Location_ID' => '',
                'Location_Code' => '',
                'Department_ID' => '',
                'Department_Code' => '',
                'Recipient_ID' => '',
                'Recipient_Identification' => '',
                'Recipient_Mail' => $attributes['email'],
                'Recipient_Name' => '',
                'Origin' => '2',
                'Description' => 'An order has been created for :' . $attributes['description'],
                'ParentRequest' => '',
                'CI_ID' => '',
                'CI_ASSET_TAG' => '',
                'CI_NAME' => '',
                'SUBMIT_DATE' => ''
            );

            $client = new \Guzzle\Http\Client('https://wa.easyvista.com/api/v1/50005/');
            $uri = 'requests';
            $code = base64_encode(env('EV_API_LOGIN') . ':' . env('EV_API_PASSWORD'));
            $request = $client->post($uri, array(
                'content-type' => 'application/json',
                'Authorization' => 'Basic anN0ZWVsZTp3MXJlbGVzcw=='// . $code
            ));

            $data = json_encode(['requests' => [$post_data]]);
            $request->setBody($data);
            $response = $request->send();

            \Log::debug("SendEVEmail@createTicketOnEasyVista - EV Email has been sent.");
            return true;
        } catch (\Exception $e) {
            \Log::debug("SendEVEmail@createTicketOnEasyVista - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }
}