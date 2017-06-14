<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Order\Order;
use WA\DataStore\Order\OrderTransformer;
use WA\Repositories\Order\OrderInterface;

use DB;

/**
 * Order resource.
 *
 * @Resource("Order", uri="/Order")
 */
class OrdersController extends FilteredApiController
{
    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * OrdersController constructor.
     *
     * @param OrderInterface $order
     * @param Request $request
     */
    public function __construct(OrderInterface $order, Request $request)
    {
        parent::__construct($order, $request);
        $this->order = $order;
    }

    /**
     * Update contents of a Order.
     *
     * @param $id
     *
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)
    {
        $success = true;
        $code = 'conflict';
        $data_apps = $data_serviceitems = $data_devices = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'orders')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if(!$this->addFilterToTheRequest("store", $request)) {
            $error['errors']['autofilter'] = Lang::get('messages.FilterErrorNotUser');
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        DB::beginTransaction();

        /*
         * Now we can update the Order.
         */
        try {
            $data = $request->all()['data'];
            $data['attributes']['id'] = $id;
            $order = $this->order->update($data['attributes']);

            if ($order == 'notExist') {
                $success = false;
                $code = 'notexists';
                $error['errors']['order'] = Lang::get('messages.NotExistClass', ['class' => 'Order']);
                //$error['errors']['Message'] = $e->getMessage();
            }

            if ($order == 'notSaved') {
                $success = false;
                $error['errors']['order'] = Lang::get('messages.NotSavedClass', ['class' => 'Order']);
                //$error['errors']['Message'] = $e->getMessage();
            }

        } catch (\Exception $e) {
            $success = false;
            $error['errors']['orders'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'Order', 'option' => 'updated', 'include' => '']);
            //$error['errors']['Message'] = $e->getMessage();
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships']) && $success) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['apps']) && $success) {
                if (isset($dataRelationships['apps']['data'])) {
                    $data_apps = $this->parseJsonToArray($dataRelationships['apps']['data'], 'apps');
                    try {
                        $order->apps()->sync($data_apps);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['apps'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Order', 'option' => 'updated', 'include' => 'Apps']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['devicevariations']) && $success) {
                if (isset($dataRelationships['devicevariations']['data'])) {
                    $data_devices = $this->parseJsonToArray($dataRelationships['devicevariations']['data'], 'devicevariations');
                    try {
                        $order->deviceVariations()->sync($data_devices);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Order', 'option' => 'updated', 'include' => 'DeviceVariations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            DB::commit();
            return $this->response()->item($order, new OrderTransformer(),
                ['key' => 'orders'])->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes[$code]);
        }
    }

    /**
     * Create a new Order.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $success = true;
        $data_apps = $data_devices = array();

        /*
         * Checks if Json has data, data-type & data-attributes.
         */
        if (!$this->isJsonCorrect($request, 'orders')) {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }

        if(!$this->addFilterToTheRequest("create", $request)) {
            $error['errors']['autofilter'] = Lang::get('messages.FilterErrorNotUser');
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        DB::beginTransaction();

        /*
         * Now we can create the Order.
         */
        try {
            $data = $request->all()['data'];
            $order = $this->order->create($data['attributes']);

            if(!$order){
                $error['errors']['Order'] = 'The Order has not been created, some data information is wrong.';
                return response()->json($error)->setStatusCode(409);
            }
        } catch (\Exception $e) {
            $success = false;
            $error['errors']['orders'] = Lang::get('messages.NotOptionIncludeClass',
                ['class' => 'Order', 'option' => 'created', 'include' => '']);
            $error['errors']['Message'] = $e->getMessage();
        }

        /*
         * Check if Json has relationships to continue or if not and commit + return.
         */
        if (isset($data['relationships']) && $success) {
            $dataRelationships = $data['relationships'];

            if (isset($dataRelationships['apps'])) {
                if (isset($dataRelationships['apps']['data'])) {
                    $data_apps = $this->parseJsonToArray($dataRelationships['apps']['data'], 'apps');
                    try {
                        $order->apps()->sync($data_apps);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['Apps'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Order', 'option' => 'created', 'include' => 'Apps']);
                        $error['errors']['Message'] = $e->getMessage();
                    }
                }
            }

            if (isset($dataRelationships['devicevariations']) && $success) {
                if (isset($dataRelationships['devicevariations']['data'])) {
                    $data_devices = $this->parseJsonToArray($dataRelationships['devicevariations']['data'], 'devicevariations');
                    try {
                        $order->deviceVariations()->sync($data_devices);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['devicevariations'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Order', 'option' => 'updated', 'include' => 'DeviceVariations']);
                        //$error['errors']['Message'] = $e->getMessage();
                    }
                }
            }
        }

        if ($success) {
            if(env('EV_ENABLED')) {
                if(!$this->createTicketOnEasyVista($order)) {
                    $error['errors']['orders'] = "Create Ticket on EasyVista Error";
                    return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                }    
            }            
            $res = $this->sendConfirmationEmail($data['attributes']['userId'], 'Order');
            if(!$res) {
                DB::rollBack();
                $error['errors']['emailnotification'] = "The Email Notification has not been sent.";
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }
            DB::commit();

            event(new \WA\Events\Handlers\CreateOrder($order));
            return $this->response()->item($order, new OrderTransformer(), ['key' => 'orders'])
                        ->setStatusCode($this->status_codes['created']);
        } else {
            DB::rollBack();
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    /**
     * Delete a Order.
     *
     * @param $id
     */
    public function delete($id)
    {
        if(!$this->addFilterToTheRequest("delete", null)) {
            $error['errors']['autofilter'] = Lang::get('messages.FilterErrorNotUser');
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $order = Order::find($id);
        if ($order != null) {
            $this->order->deleteById($id);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotExistClass', ['class' => 'Order']);
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        $order = Order::find($id);
        if ($order == null) {
            return array("success" => true);
        } else {
            $error['errors']['delete'] = Lang::get('messages.NotDeletedClass', ['class' => 'Order']);
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }

    private function createTicketOnEasyVista($order) {

        try {
            $user = \WA\DataStore\User\User::find($order->userId);
            $address = \WA\DataStore\Address\Address::find($order->addressId);
            $service = \WA\DataStore\Service\Service::find($order->serviceId);
            $package = \WA\DataStore\Package\Package::find($order->packageId);
            $devicevariations = $order->devicevariations;



            $code = base64_encode(env('EV_API_LOGIN') . ':' . env('EV_API_PASSWORD'));
            $attributes = $this->makeTheStringWithOrderAttributes($order, $user, $address, $package, $service, $devicevariations);
            \Log::debug("OrdersController@createTicketOnEasyVista - attributes: " . print_r($attributes, true));

            $client = new \Guzzle\Http\Client('https://wa.easyvista.com/api/v1/' . env('EV_API_ACCOUNT') .'/');

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

    private function makeTheStringWithOrderAttributes($order, $user, $address, $package, $service, $devicevariations) {

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
