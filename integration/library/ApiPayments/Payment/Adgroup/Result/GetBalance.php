<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/18/19
 * Time: 4:39 PM
 */

namespace Pyrobyte\ApiPayments\Payment\Adgroup\Result;


use Pyrobyte\ApiPayments\Result\ResultAbstract;

class GetBalance extends ResultAbstract
{
    private $balance = null;

    public function __construct($balance)
    {
        $this->balance = $balance;
    }

    /**
     * Get balance
     * @return null
     */
    public function getBalance()
    {
        return $this->balance;
    }
}