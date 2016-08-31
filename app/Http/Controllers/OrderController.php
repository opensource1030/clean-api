<?php
namespace WA\Http\Controllers;

use Order;
use WA\DataStore\Order\OrderTransformer;
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
        $order = $this->order->getAllOrder();
        return $this->response()->collection($order, new OrderTransformer(),['key' => 'order']);

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
        $order = $this->order->byId($id);
        return $this->response()->item($order, new OrderTransformer(), ['key' => 'order']);
    }

    /**
     * Update contents of a Order
     *
     * @param $id
     * @return \Dingo\Api\Http\Response
     */
    public function store($id, Request $request)   
    {
        $data = $request->all();       
        $data['id'] = $id;
        $order = $this->order->update($data);
        return $this->response()->item($order, new OrderTransformer(), ['key' => 'order']);
    }

    /**
     * Create a new Order
     *
     * @return \Dingo\Api\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->all();
        $order = $this->order->create($data);
        return $this->response()->item($order, new OrderTransformer(), ['key' => 'order']);
    }

    /**
     * Delete a Order
     *
     * @param $id
     */
    public function delete($id)
    {
        $this->order->deleteById($id);
        $this->index();
    }
}