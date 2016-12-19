<?php

namespace WA\Repositories\CompanyCurrentBillMonth;

use WA\Repositories\RepositoryInterface;

interface CompanyCurrentBillMonthInterface extends RepositoryInterface
{

    /**
     * CurrentBillMonth by the id.
     *
     * @param int $id
     *
     * @return object object of the CurrentBillMonth information
     */
    public function byId($id);

    /**
     * Get CurrentBillMonths Transformer.
     *
     * @return mixed
     */
    public function getTransformer();

}
