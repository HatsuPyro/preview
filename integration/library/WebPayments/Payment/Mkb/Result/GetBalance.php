<?php


namespace Pyrobyte\WebPayments\Payment\Mkb\Result;


class GetBalance extends \Pyrobyte\WebPayments\Payment\Result
{
    public $balance;
    public $currency;

    public function getBalance()
    {
        return $this->balance;
    }
}