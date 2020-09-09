<?php

namespace App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions;

use Pyrobyte\SmsPayments\Payment\Qiwi\Action\PaySelfPhone as QiwiPayAction;

/**
 * Адаптер вывода с киви на номер телефона кошелька
 * Class PaySelfPhone
 * @package App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions
 */
class PaySelfPhone extends PayActionAbstract
{
    private $sum = null;

    public function __construct($sum)
    {
        $this->sum = $sum;
    }

    /**
     * Получает результат выполнения работы клиента/операции
     * @return mixed
     */
    protected function getClientResult()
    {
        $result = $this->client->call(new QiwiPayAction($this->sum));

        return $result;
    }
}