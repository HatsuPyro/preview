<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.10.2018
 * Time: 14:49
 */

namespace Pyrobyte\SmsPayments\Result;


class GetBalanceAbstract
{
    protected $balance = null;

    public function __construct($balance)
    {
        $this->balance = $balance;
    }

    public function getBalance()
    {
        return $this->balance;
    }
}