<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 18.11.19
 * Time: 14:29
 */

namespace Pyrobyte\ApiPayments\Payment\P2P\Result;

use Pyrobyte\ApiPayments\Result\ResultAbstract;

class GetTransactions extends ResultAbstract
{
    private $transactions = [];

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * Get transactions
     * @return array
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }
}