<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/18/19
 * Time: 6:12 PM
 */

namespace Pyrobyte\ApiPayments\Payment\Adgroup\Result;


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