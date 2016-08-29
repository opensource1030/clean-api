<?php

namespace WA\Repositories\Order;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentOrder
 *
 * @package WA\Repositories\Order
 */
class EloquentOrder extends AbstractRepository implements OrderInterface
{
    /**
     * Update Order
     *
     * @param array $data
     * @return bool
     */
    public function update(array $data)
    {
        $order = $this->model->find($data['id']);

        if(!$order)
        {
            return false;
        }

        $order->status =  isset($data['status']) ? $data['status'] : null ;
        $order->created_at = isset($data['created_at']) ? $data['created_at'] : null;
        $order->idEmployee = isset($data['idEmployee']) ? $data['idEmployee'] : null;
        $order->idPackage = isset($data['idPackage']) ? $data['idPackage'] : 0;

        if(!$order->save()) {
            return false;
        }

        return $order;

    }

    /**
     * Get an array of all the available order.
     *
     * @return Array of order
     */
    public function getAllOrder()
    {
        $order =  $this->model->all();
        return $order;
    }

    /**
     * Create a new order
     *
     * @param array $data
     * @return bool|static
     */
    public function create(array $data)
    {

        $orderData = [
            "status" =>  isset($data['status']) ? $data['status'] : null ,
            "created_at" => isset($data['created_at']) ? $data['created_at'] : null,
            "idEmployee" => isset($data['idEmployee']) ? $data['idEmployee'] : null,
            "idPackage" => isset($data['idPackage']) ? $data['idPackage'] : 0,
        ];

        $order = $this->model->create($orderData);

        if(!$order) {
            return false;
        }

        return $order;
    }

    /**
     * Delete a order.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true)
    {
        if (!$this->model->find($id)) {
            return false;
        }

        if (!$soft) {
            $this->model->forceDelete($id);
        }

        return $this->model->destroy($id);
    }
}