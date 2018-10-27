<?php

namespace WA\Repositories\Payment;

use WA\Repositories\AbstractRepository;

/**
 * Class EloquentPayment.
 */
class EloquentPayment extends AbstractRepository implements PaymentInterface
{
    /**
     * Update Payment.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data)
    {
        $payment = $this->model->find($data['id']);

        if (!$payment) {
            return 'notExist';
        }

        if (isset($data['success'])) {
            $payment->success = $data['success'];
        }
        if (isset($data['details'])) {
            $payment->details = $data['details'];
        }
        if (isset($data['transactionId'])) {
            $payment->transactionId = $data['transactionId'];
        }
        if (isset($data['userId'])) {
            $payment->userId = $data['userId'];
        }
        if (isset($data['orderId'])) {
            $payment->orderId = $data['orderId'];
        }

        if (!$payment->save()) {
            return 'notSaved';
        }

        return $payment;
    }

    /**
     * Create a new Payment.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data)
    {
        $paymentData = [
            "success" => isset($data['success']) ? $data['success'] : true,
            "details" => isset($data['details']) ? $data['details'] : '',
            "transactionId" => isset($data['transactionId']) ? $data['transactionId'] : 0,
            "userId" => isset($data['userId']) ? $data['userId'] : null,
            "orderId" =>  isset($data['orderId']) ? $data['orderId'] : null,
        ];

        $payment = $this->model->create($paymentData);

        if (!$payment) {
            return false;
        }

        return $payment;
    }

    /**
     * Delete a Payment.
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
