<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.10.2018
 * Time: 11:10
 */

namespace App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions;

use Pyrobyte\SmsPayments\Payment\Qiwi\Action\PayPhone as QiwiPayAction;

/**
 * Адаптер вывода с киви на номер телефона
 * Class PayPhone
 * @package App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions
 */
class PayPhone extends PayActionAbstract
{
    private $phoneNumber = null;
    private $sum = null;

    public function __construct($phoneNumber, $sum)
    {
        $this->phoneNumber = $phoneNumber;
        $this->sum = $sum;
    }

    /**
     * Получает результат выполнения работы клиента/операции
     * @return mixed
     */
    protected function getClientResult()
    {
        $result = $this->client->call(new QiwiPayAction($this->phoneNumber, $this->sum));

        return $result;
    }
}