<?php

namespace WA\Repositories\Order;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentOrder.
 */
class EloquentJobOrder extends AbstractRepository implements OrderJobInterface
{
    /**
     * Update OrderJob.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $orderJob = $this->model->find($data['id']);

        if (!$orderJob) {
            return 'notExist';
        }

        if (isset($data['orderId'])) {
            $order->orderId = $data['orderId'];
        }
        if (isset($data['statusBefore'])) {
            $order->statusBefore = $data['statusBefore'];
        }
        if (isset($data['statusAfter'])) {
            $order->statusAfter = $data['statusAfter'];
        }
        
        if (!$order->save()) {
            return 'notSaved';
        }

        return $order;
    }

    /**
     * Create a new order.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        if (!isset($data['orderId'])) {
            return false;
        }

        $orderData = [
            'orderId'       => $data['orderId'],
            'statusBefore'  => isset($data['statusBefore'])     ? $data['statusBefore']     : '',
            'statusAfter'   => isset($data['statusAfter'])      ? $data['statusAfter']      : ''
        ];

        $order = $this->model->create($orderData);

        if (!$order) {
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
