<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/9/19
 * Time: 5:12 PM
 */

namespace Pyrobyte\SmsPayments\Result;


class GetTransactionsAbstract extends ResultAbstract
{
    private $transactions = [];

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * Получает транзакции
     * @return array
     */
    public function getTransactions(): array
    {
        return $this->transactions;
    }
}