<?php

namespace WA\DataStore\Payment;

use WA\DataStore\FilterableTransformer;

/**
 * Class AddressTransformer.
 */
class PaymentTransformer extends FilterableTransformer
{
    protected $availableIncludes = [
        'users',
        'orders',
    ];

    /**
     * @param Payment $payment
     *
     * @return array
     */
    public function transform(Payment $payment)
    {
        return [
            'id'                => (int)$payment->id,
            'success'           => $payment->success,
            'details'           => $payment->details,
            'transactionId'     => $payment->transactionId,
            'userId'            => (int)$payment->userId,
            'orderId'           => (int)$payment->orderId,
            'created_at'        => $payment->created_at,
            'updated_at'        => $payment->updated_at,
        ];
    }
}
