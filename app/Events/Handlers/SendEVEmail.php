<?php

/**
 * CreateOrder - Gets the event received by the Single Sign On.
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
        
        $order = $event->order;
        
        try {
            $user = \WA\DataStore\User\User::find($order->userId);
            $address = \WA\DataStore\Address\Address::find($order->addressId);
            $service = \WA\DataStore\Service\Service::find($order->serviceId);
            $package = \WA\DataStore\Package\Package::find($order->packageId);
            $devicevariations = $order->devicevariations;

            $code = base64_encode(env('EV_API_LOGIN') . ':' . env('EV_API_PASSWORD'));
            $attributes = $this->easyVistaStringDescription($order, $user, $address, $package, $service, $devicevariations);
            
            $client = new \Guzzle\Http\Client('https://wa.easyvista.com/api/v1/50005/');

            $packageAC = isset($package->approvalCode) ? $package->approvalCode : '';

            $uri = 'requests';
            $post_data = array(
                'Catalog_GUID' => '',
                'Catalog_Code' => $packageAC,
                'AssetID' => '',
                'AssetTag' => '',
                'ASSET_NAME' => '',
                'Urgency_ID' => '2',
                'Severity_ID' => '41',
                'External_reference' => '',
                'Phone' => '',
                'Requestor_Identification' => '',
                'Requestor_Mail' => $user->email,
                'Requestor_Name' => '',
                'Location_ID' => '',
                'Location_Code' => '',
                'Department_ID' => '',
                'Department_Code' => '',
                'Recipient_ID' => '',
                'Recipient_Identification' => '',
                'Recipient_Mail' => $user->email,
                'Recipient_Name' => '',
                'Origin' => '2',
                'Description' => 'An order has been created for :' . $attributes,
                'ParentRequest' => '',
                'CI_ID' => '',
                'CI_ASSET_TAG' => '',
                'CI_NAME' => '',
                'SUBMIT_DATE' => ''
            );

            $data = json_encode(['requests' => [$post_data]]);
            $request = $client->post($uri, array(
                'content-type' => 'application/json',
                'Authorization' => 'Basic anN0ZWVsZTp3MXJlbGVzcw=='// . $code
            ));

            $request->setBody($data);
            $response = $request->send();
            \Log::debug("OrdersController@createTicketOnEasyVista - response: " . print_r($response, true));
            return true;
        } catch (\Exception $e) {
            \Log::debug("OrdersController@createTicketOnEasyVista - e: " . print_r($e->getMessage(), true));
            return false;
        }
    }

    private function easyVistaStringDescription($order, $user, $address, $package, $service, $devicevariations) {

        $company = \WA\DataStore\Company\Company::find($user->companyId);
        $attributes = '';

        // Company.
        $attributes = $attributes .
            '<h2><strong>' . $company->name .
            ' - ' . $order->orderType . 
            ' - ' . $user->username .
            '</strong></h2>';

        $packageName = isset($package->name) ? $package->name : '';

        // Package.
        $attributes = $attributes .
            '<h3><strong>Package Name: </strong>' . $packageName .
            '</h3>';

        $attributes = $attributes .
            '<hr />';

        // User
        $departmentUdl = '';
        $costCenterUdl = '';
        $udlValues = $user->udlvalues;
        foreach ($udlValues as $udlValue) {
            $udl = \WA\DataStore\Udl\Udl::find($udlValue->udlId);
            if ($udl->name == 'Department') {
                $departmentUdl = $udlValue->name;
            }

            if ($udl->name == 'Cost Center') {
                $costCenterUdl = $udlValue->name;
            }
        }

        $activeLogin = \Auth::user();
        $attributes = $attributes .
        '<h3 class="heading2">User Info:</h3>' .
        '<p>' .
            '<strong>Username:</strong>&nbsp;' . $user->username .
            '<br /><strong>Email:</strong>&nbsp;' . $user->email .
            '<br /><strong>Supervisor Email:</strong> ' . $user->supervisorEmail .
            '<br /><strong>Department:</strong> ' . $departmentUdl .
            '<br /><strong>Cost Center:</strong> ' . $costCenterUdl .
        '</p>' .
        '<p>' .
            '<strong>Entered by:</strong>&nbsp;' . $activeLogin->username .
        '</p>';

        $attributes = $attributes .
            '<hr />';

        $smartphone = '';
        $accessories = '';
        if ($devicevariations == null) {
            foreach ($devicevariations as $dv) {
                if ($dv->devices->devicetypes->name == 'Smartphone') {
                    $smartphone = $dv;
                }

                if ($dv->devices->devicetypes->name == 'Accessory') {
                    if ($accessories == '') {
                        $accessories = $accessories . ', ';
                    }
                    $accessories = $accessories . $dv->name;
                }
            }    
        }
        
        if ($smartphone == '') {
            $make = '';
            $model = '';
        } else {
            $make = $smartphone->devices->make;
            $model = $smartphone->devices->model;
        }

        $domVo = $domDa = $domMe = $intVo = $intDa = $intMe = '';
        if ($service == null) {
            $CarrierName = $order->deviceCarrier;
        } else {
            $CarrierName = $service->carriers->name;
            
            foreach ($service->serviceitems as $si) {
                if ($si->domain == 'domestic') {
                    if ($si->category == 'voice') {
                        $domVo = $si->value . ' ' . $si->unit;
                    } else if ($si->category == 'data') {
                        $domDa = $si->value . ' ' . $si->unit;
                    } else if ($si->category == 'messages') {
                        $domMe = $si->value . ' ' . $si->unit;
                    } else {
                        // NOTHING.
                    }
                } else if ($si->domain == 'international') {
                    if ($si->category == 'voice') {
                        $intVo = $si->value . ' ' . $si->unit;
                    } else if ($si->category == 'data') {
                        $intDa = $si->value . ' ' . $si->unit;
                    } else if ($si->category == 'messages') {
                        $intMe = $si->value . ' ' . $si->unit;
                    } else {
                        // NOTHING.
                    }
                }
            }
        }

        //
        $attributes = $attributes .
            '<h3 class="heading2">Device&nbsp;Info:</h3>' .
            '<p>' .
                '<strong>Mobile Number:</strong> ' . $order->servicePhoneNo .
                '<br />' .
                '<strong>Carrier:</strong> ' . $CarrierName .
                '<br />' .
                '<strong>Make/Model:</strong> ' . $make . ' ' . $model .
                '<br />' .
                '<strong>Accessories:</strong> ' . $accessories .
            '</p>';

        $attributes = $attributes .
            '<hr />';

        $attributes = $attributes .
            '<h3 class="heading2">Mobile Service Info:</h3>' .
            '<p>' .
                '<strong>Domestic Voice:</strong>' . $domVo .
                '<br />' .
                '<strong>Domestic Data:</strong>' . $domDa .
                '<br />' .
                '<strong>Domestic Messaging:</strong>' . $domMe .
                '<br />' .
                '<strong>International Voice:</strong>' . $intVo .
                '<br />' .
                '<strong>International Data:</strong>' . $intDa .
                '<br />' .
                '<strong>International Messaging:</strong>' . $intMe .
            '</p>';

        $attributes = $attributes .
            '<hr />';

        $attributes = $attributes .
            '<h3 class="heading2">Shipping Info:</h3>' .
            '<p>' . $company->name .
                '<br />' . $address->name .
                '<br />' . $address->city . ', ' . $address->state . ', ' . $address->postalCode .
                '<br />Attn.&nbsp;' . $user->username .
            '</p>';
/*
        $attributes = $attributes .
            '<hr />';

        $attributes = $attributes .
            '<h3 class="heading2">Comments:</h3><p>Open comments field.</p>';
*/
        return $attributes;
    }
}