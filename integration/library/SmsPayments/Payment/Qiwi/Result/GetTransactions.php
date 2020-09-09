<?php
namespace Pyrobyte\SmsPayments\Payment\Qiwi\Result;

/**
 * Результат получения транзакций из смс оповещений qiwi
 * Class GetTransactions
 * @package Pyrobyte\SmsPayments\Payment\Qiwi\Result
 */
class GetTransactions
{
    public $transactions = [];

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * Получает транзакции списывания средств
     * @return array
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}