<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.10.2018
 * Time: 17:32
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\Pay;


use Pyrobyte\SmsPayments\Services\MessagesProcessor\MessagesProcessorStateAbstract;

class GetPayResult extends MessagesProcessorStateAbstract
{
    protected $name = 'Получение сообщения отправки платежа';

    public function doProcess($message)
    {
        //Платеж на Tele2 №9586303950 сумма 10.00 принят.
        //Платеж на MIR perevody №2200800202628415 сумма 10.00 принят
        //Перевод в пользу 79006291409 на сумму 10.00 принят к проведению
        //Платеж на Перевод на карту Visa RUS №4276020710138170 сумма 10.00 принят
        $paymentSucceed = (bool)preg_match('/.*?((платеж|перевод).*?принят)/imu', $message->get());

        return $paymentSucceed ? true : null;
    }
}