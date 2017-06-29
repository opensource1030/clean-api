<?php

namespace WA\Repositories\Order;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentOrder.
 */
class EloquentOrder extends AbstractRepository implements OrderInterface
{
    /**
     * Update Order.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $order = $this->model->find($data['id']);

        if (!$order) {
            return 'notExist';
        }

        /* The Status will not be updated directly.
         * We will use the OrderSendEmailEventSubscriber to do that.
         *
        if (isset($data['status'])) {
            $order->status = $data['status'];
        }*/
        if (isset($data['orderType'])) {
            $order->orderType = $data['orderType'];
        }
        if (isset($data['serviceImei'])) {
            $order->serviceImei = $data['serviceImei'];
        }
        if (isset($data['servicePhoneNo'])) {
            $order->servicePhoneNo = $data['servicePhoneNo'];
        }
        if (isset($data['serviceSim'])) {
            $order->serviceSim = $data['serviceSim'];
        }
        if (isset($data['deviceImei'])) {
            $order->deviceImei = $data['deviceImei'];
        }
        if (isset($data['deviceCarrier'])) {
            $order->deviceCarrier = $data['deviceCarrier'];
        }
        if (isset($data['deviceSim'])) {
            $order->deviceSim = $data['deviceSim'];
        }
        if (isset($data['userId'])) {
            $order->userId = $data['userId'];
        }
        if (isset($data['packageId'])) {
            $order->packageId = $data['packageId'];
        }
        if (isset($data['deviceId'])) {
            $order->deviceId = $data['deviceId'];
        }
        if (isset($data['serviceId'])) {
            $order->serviceId = $data['serviceId'];
        }
        if (isset($data['addressId'])) {
            $order->addressId = $data['addressId'];
        }

        if (!$order->save()) {
            return 'notSaved';
        }

        return $order;
    }

    /**
     * Get an array of all the available order.
     *
     * @return array of order
     */
    public function getAllOrder()
    {
        $order = $this->model->all();

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
        if (!isset($data['userId'])) {
            return false;
        }

        $orderData = [
            'status'            => 'New',
            'orderType'         => isset($data['orderType'])        ? $data['orderType']        : null,
            'serviceImei'       => isset($data['serviceImei'])      ? $data['serviceImei']      : '',
            'servicePhoneNo'    => isset($data['servicePhoneNo'])   ? $data['servicePhoneNo']   : '',
            'serviceSim'        => isset($data['serviceSim'])       ? $data['serviceSim']       : '',
            'deviceImei'        => isset($data['deviceImei'])       ? $data['deviceImei']       : '',
            'deviceCarrier'     => isset($data['deviceCarrier'])    ? $data['deviceCarrier']    : '',
            'deviceSim'         => isset($data['deviceSim'])        ? $data['deviceSim']        : '',
            'userId'            => isset($data['userId'])           ? $data['userId']           : null,
            'packageId'         => isset($data['packageId'])        ? $data['packageId']        : null,
            'deviceId'          => isset($data['deviceId'])         ? $data['deviceId']         : null,
            'serviceId'         => isset($data['serviceId'])        ? $data['serviceId']        : null,
            'addressId'         => isset($data['addressId'])        ? $data['addressId']        : null,
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

    public function addFilterToTheRequest($companyId) {
        $aux[]='[users.companyId]=' . $companyId . '[and][packages.companyId]=' . $companyId;
        return $aux;
    }

    /**
     * Check if the Model and/or its relationships are related to the Company of the User.
     *
     * @param JSON  $json : The Json request.
     * @param int  $companyId
     *
     * @return Boolean
     */
    public function checkModelAndRelationships($json, $companyId) {
        $ok = true;
        $attributes = $json->data->attributes;

        $user = \WA\DataStore\User\User::find($attributes->userId);
        $ok = $ok && ($user->companyId == $companyId);

        $pack = \WA\DataStore\Package\Package::find($attributes->packageId);
        $ok = $ok && ($pack->companyId == $companyId);

        $service = \WA\DataStore\Service\Service::find($attributes->serviceId);
        foreach ($service->packages as $value) {
            $ok = $ok && ($value->companyId == $companyId);
        }

        return $ok;
    }
}
