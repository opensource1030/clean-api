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
            $this->updateOrderEvent($order, $data['attributes']);
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
            DB::commit();
            // event(new \WA\Events\Handlers\CreateOrder($order));
            $this->createOrderEvent($order);
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

    private function createOrderEvent($order) {
        \Log::debug("OrdersController@createOrderEvent");
        $workflow = \Workflow::get($order);
        $workflow->apply($order, 'create');
        $order->save();
    }

    private function updateOrderEvent($order, $attributes) {
        \Log::debug("OrdersController@updateOrderEvent");
        \Log::debug($attributes['status']);
        \Log::debug($order->status);

        $workflow = \Workflow::get($order);

        if ($order->status == 'Approval' && $attributes['status'] == 'Deliver') {
            \Log::debug('accept');
        } else if ($order->status == 'Approval' && $attributes['status'] == 'Denied') {
            \Log::debug('deny');
        } else if ($order->status == 'Deliver' && $attributes['status'] == 'Delivered') {
            \Log::debug('send');
        } else {
            // NOTHING
        }
    }
}
