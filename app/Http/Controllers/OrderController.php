<?php
namespace WA\Http\Controllers;

use WA\DataStore\Order\OrderTransformer;
use WA\DataStore\Order\Order;
use WA\Repositories\Order\OrderInterface;
use Illuminate\Http\Request;

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
            return response()->json($error)->setStatusCode($this->errors['notexists']);
        }

        // Dingo\Api\src\Http\Response\Factory.php
        // Dingo\Api\src\Http\Transformer\Factory.php

        return $this->response()->item($order, new OrderTransformer(),['key' => 'orders'])->setStatusCode($this->errors['created']);
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
                return $this->response()->item($order, new OrderTransformer(), ['key' => 'orders']);
            } catch (\Exception $e){
                $error['errors']['images'] = 'the Order can not be updated';
                //$error['errors']['imagesMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = 'Json is Invalid';
        }

        return response()->json($error)->setStatusCode($this->errors['conflict']);
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
                return $this->response()->item($order, new OrderTransformer(), ['key' => 'orders']);
            } catch (\Exception $e){
                $error['errors']['images'] = 'the Order can not be created';
                //$error['errors']['imagesMessage'] = $e->getMessage();
            }
        } else {
            $error['errors']['json'] = 'Json is Invalid';
        }

        return response()->json($error)->setStatusCode($this->errors['conflict']);
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
            return response()->json($error)->setStatusCode($this->errors['notexists']);
        }
        
        $this->index();
        $order = Order::find($id);        
        if($order == null){
            return array("success" => true);
        } else {
            $error['errors']['delete'] = 'the Order has not been deleted';   
            return response()->json($error)->setStatusCode($this->errors['conflict']);
        }
    }
}