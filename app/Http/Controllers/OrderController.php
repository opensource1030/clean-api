<?php

namespace WA\Http\Controllers;

use Illuminate\Http\Request;

use WA\DataStore\Order\OrderTransformer;
use WA\DataStore\Order\Order;
use WA\Repositories\Order\OrderInterface;

/**
 * Order resource.
 *
 * @Resource("Order", uri="/Order")
 */
class OrderController extends ApiController
{
    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * Order Controller constructor
     *
     * @param OrderInterface $Order
     */
    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }

    /**
     * Show all Order
     *
     * Get a payload of all Order
     *
     */
    public function index()
    {
        $criteria = $this->getRequestCriteria();
        $this->order->setCriteria($criteria);
        $order = $this->order->byPage();
      
        $response = $this->response()->withPaginator($order, new OrderTransformer(),['key' => 'orders']);
        $response = $this->applyMeta($response);
        return $response;
    }

    /**
     * Show a single Order
     *
     * Get a payload of a single Order
     *
     * @Get("/{id}")
     */
    public function show($id)
    {
        $order = Order::find($id);
        if($order == null){
            $error['errors']['get'] = 'the Order selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }

        return $this->response()->item($order, new OrderTransformer(),['key' => 'orders'])->setStatusCode($this->status_codes['created']);
    }

    /**
     * Update contents of a Order
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        if($this->isJsonCorrect($request, 'orders')){
            try {
                $data = $request->all()['data']['attributes'];
                $data['id'] = $id;
                $order = $this->order->update($data);
                return $this->response()->item($order, new OrderTransformer(), ['key' => 'orders'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e){
                $error['errors']['orders'] = 'the Order has not been updated';
                //$error['errors']['ordersMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = 'Json is Invalid';
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Create a new Order
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        if($this->isJsonCorrect($request, 'orders')){
            try {
                $data = $request->all()['data']['attributes'];
                $order = $this->order->create($data);
                return $this->response()->item($order, new OrderTransformer(), ['key' => 'orders'])->setStatusCode($this->status_codes['created']);
            } catch (\Exception $e){
                $error['errors']['orders'] = 'the Order has not been created';
                //$error['errors']['ordersMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = 'Json is Invalid';
        }

        return response()->json($error)->setStatusCode($this->status_codes['conflict']);
    }

    /**
     * Delete a Order
     *
     * @param $id
     */
    public function delete($id)
    {
        $order = Order::find($id);
        if($order <> null){
            $this->order->deleteById($id);
        } else {
            $error['errors']['delete'] = 'the Order selected doesn\'t exists';   
            return response()->json($error)->setStatusCode($this->status_codes['notexists']);
        }
        
        $this->index();
        $order = Order::find($id);        
        if($order == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the Order has not been deleted';   
            return response()->json($error)->setStatusCode($this->status_codes['conflict']);
        }
    }
}