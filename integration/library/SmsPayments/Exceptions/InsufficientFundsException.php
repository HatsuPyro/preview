<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 29.10.2018
 * Time: 11:05
 */

namespace Pyrobyte\SmsPayments\Exceptions;


class InsufficientFundsException extends GotErrorMessageException
{
    private $balance = null;

    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    public function getBalance()
    {
        return $this->balance;
    }
}