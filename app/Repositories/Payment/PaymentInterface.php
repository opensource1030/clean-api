<?php

namespace WA\Repositories\Payment;

use WA\Repositories\RepositoryInterface;

/**
 * Interface PaymentInterface.
 */
interface PaymentInterface extends RepositoryInterface
{
    /**
     * Create Payment.
     *
     * @param array $data
     *
     * @return bool|static
     */
    public function create(array $data);

    /**
     * Update Payment.
     *
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data);

    /**
     * Delete Payment.
     *
     * @param int  $id
     * @param bool $soft true soft deletes
     *
     * @return bool
     */
    public function delete($id, $soft = true);
}
