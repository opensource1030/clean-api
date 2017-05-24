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

            if (isset($dataRelationships['serviceitems']) && $success) {
                if (isset($dataRelationships['serviceitems']['data'])) {
                    $data_serviceitems = $this->parseJsonToArray($dataRelationships['serviceitems']['data'], 'serviceitems');
                    try {
                        $order->serviceitems()->sync($data_serviceitems);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['serviceitems'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Order', 'option' => 'updated', 'include' => 'Service Items']);
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
        $data_apps = $data_serviceitems = $data_devices = array();

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

            if (isset($dataRelationships['serviceitems'])) {
                if (isset($dataRelationships['serviceitems']['data'])) {
                    $data_serviceitems = $this->parseJsonToArray($dataRelationships['serviceitems']['data'], 'serviceitems');
                    try {
                        $order->serviceitems()->sync($data_serviceitems);
                    } catch (\Exception $e) {
                        $success = false;
                        $error['errors']['serviceitems'] = Lang::get('messages.NotOptionIncludeClass',
                            ['class' => 'Order', 'option' => 'created', 'include' => 'Service Items']);
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
            if(!$this->createTicketOnEasyVista($order)) {
                $error['errors']['orders'] = "Create Ticket on EasyVista Error";
                return response()->json($error)->setStatusCode($this->status_codes['conflict']);
            }
            $res = $this->sendConfirmationEmail($data['attributes']['userId'], 'Order');
            DB::commit();
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
            $attributes = $this->makeTheStringWithOrderAttributes($user, $address, $package, $service, $devicevariations);
            \Log::debug("OrdersController@createTicketOnEasyVista - attributes: " . print_r($attributes, true));

            $client = new \Guzzle\Http\Client('https://wa.easyvista.com/api/v1/50005/');
            $uri = 'requests';
            $post_data = array(
                'Catalog_GUID' => '',
                'Catalog_Code' => $package->approvalCode,
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

    private function makeTheStringWithOrderAttributes($user, $address, $package, $service, $devicevariations) {
        $attributes = '';

        $attributes = $attributes .
            '<bold>Username:</bold> ' . $user->username .
            ' with email: ' . $user->email .
            ', with supervisor: ' . $user->supervisorEmail . '<br>';

        $attributes = $attributes .
            ', <bold>Address Name:</bold> ' . $address->name .
            ', from ' . $address->city .
            ' ( ' . $address->state . ' - ' . $address->country . ' )<br>';

        /*
        if ($package != null) {
            $attributes = $attributes . '<bold>Package Name:</bold> ' . $package->name . '<br>';
        }
        */

        if ($service != null) {
            $attributes = $attributes . '<bold>Service Name:</bold> ' . $service->title . ', ';
            foreach ($service->serviceitems as $si) {
                if ($si->domain == 'domestic' || $si->domain == 'international') {
                    if ($si->value > 0) {
                        $attributes = $attributes .
                            $si->domain . ' ' .
                            $si->category . ': ' .
                            $si->value . ' ' .
                            $si->unit . ', ';
                    }
                }
            }
        }

        $attributes = $attributes . '<br>';

        if (count($devicevariations) > 0) {
            foreach ($devicevariations as $dv) {
                if(isset($dv->devices)) {
                    if (isset($dv->devices->devicetypes)) {
                        if ($dv->devices->devicetypes->name == 'Smartphone') {
                            $attributes = $attributes .
                                '<bold>Device Name</bold>: ' . $dv->devices->name . ' : ' .
                                $dv->devices->defaultPrice . ' ' .
                                $dv->devices->currency;
                            if($dv->devices->property != '') {
                                $attributes = $attributes  . ', ' . $dv->devices->property;
                            }
                        }
                    }
                }
            }
        }

        $attributes = $attributes . '.';

        return $attributes;
    }
}
