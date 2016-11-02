<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use WA\DataStore\Order\Order;
use WA\DataStore\Order\OrderTransformer;
use WA\Repositories\Order\OrderInterface;

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
        if ($this->isJsonCorrect($request, 'orders')) {
            try {
                $data = $request->all()['data']['attributes'];
                $data['id'] = $id;
                $order = $this->order->update($data);

                if ($order == 'notExist') {
                    $error['errors']['order'] = Lang::get('messages.NotExistClass', ['class' => 'Order']);
                    //$error['errors']['Message'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['notexists']);
                }

                if ($order == 'notSaved') {
                    $error['errors']['order'] = Lang::get('messages.NotSavedClass', ['class' => 'Order']);
                    //$error['errors']['Message'] = $e->getMessage();
                    return response()->json($error)->setStatusCode($this->status_codes['conflict']);
                }

                return $this->response()->item($order, new OrderTransformer(),
                    ['key' => 'orders'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['orders'] = Lang::get('messages.NotOptionIncludeClass',
                    ['class' => 'Order', 'option' => 'updated', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new Order.
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if ($this->isJsonCorrect($request, 'orders')) {
            try {
                $data = $request->all()['data']['attributes'];
                $order = $this->order->create($data);

                return $this->response()->item($order, new OrderTransformer(),
                    ['key' => 'orders'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e) {
                $error['errors']['orders'] = Lang::get('messages.NotOptionIncludeClass',
                    ['class' => 'Order', 'option' => 'created', 'include' => '']);
                //$error['errors']['Message'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = Lang::get('messages.InvalidJson');
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Delete a Order.
     *
     * @param $id
     */
    public function delete($id)
    {
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
}
