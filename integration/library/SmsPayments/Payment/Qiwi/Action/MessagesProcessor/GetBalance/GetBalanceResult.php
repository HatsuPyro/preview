<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.10.2018
 * Time: 16:03
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\GetBalance;


use Pyrobyte\SmsPayments\Services\MessagesProcessor\MessagesProcessorStateAbstract;

class GetBalanceResult extends MessagesProcessorStateAbstract
{
    protected $name = 'Получение сообщения баланса';

    public function doProcess($message)
    {
        $matches = [];
        //На счету  349.4 руб. Лимит на пополнение  14651 руб. Лимит на оборот средств  38789 руб.
        preg_match('/.*?счет.*?(\d+(.\d+)*)/imu', $message->get(), $matches);

        return $matches[1] ?? null;
    }
}