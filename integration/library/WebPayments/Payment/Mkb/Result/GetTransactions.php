<?php


namespace Pyrobyte\WebPayments\Payment\Mkb\Result;


class GetTransactions extends \Pyrobyte\WebPayments\Payment\Result
{
    public $transactions;

    public function getTransactions()
    {
        return $this->transactions;
    }
}