<?php

namespace WA\Helpers\Traits;

use WA\DataStore\AccountSummary;

/**
 * Class GetSetAccountSummary.
 */
trait GetSetAccountSummary
{
    /**
     * @var AccountSummary
     */
    protected $accountSummary;

    /**
     * @return AccountSummary
     */
    public function getAccountSummary()
    {
        return $this->accountSummary;
    }

    /**
     * @param AccountSummary $accountSummary
     */
    public function setAccountSummary(AccountSummary $accountSummary)
    {
        $this->accountSummary = $accountSummary;
    }
}
