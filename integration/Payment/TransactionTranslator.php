<?php

/**
 * Абстрактный класс конвертора транзакций
 */

namespace App\Extensions\Payment;

/**
 * Класс преобразователь информации транзакций к общему виду
 * Class TransactionTranslator
 * @package App\Extensions\Payment
 */
abstract class TransactionTranslator
{
    const DATE_FORMAT = 'Y-m-d\TH:i:s';

    const FIELD_DATE = 'date';
    const FIELD_AMOUNT = 'sum_amount';
    const FIELD_CURRENCY = 'sum_currency';
    const FIELD_STATUS = 'status';
    const FIELD_TYPE = 'type';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_ID = 'transaction_id';
    const FIELD_DESTINATION = 'destination';
    const FIELD_PAYER = 'payer';
    const FIELD_BALANCE = 'balance';
    const FIELD_STATE = 'state';
    const FIELD_COMISSION_AMOUNT = 'comission_amount';
    const FIELD_COMISSION_CURRENCY = 'comission_currency';
    const FIELD_CARD_NUMBER = 'card_number';

    protected $initialTransaction = null;

    public function __construct($initialTransaction)
    {
        $this->initialTransaction = $initialTransaction;
    }

    /**
     * Выполняет трансляцию транзакии в нужный формат
     * @return mixed
     */
    abstract public function translate();
}