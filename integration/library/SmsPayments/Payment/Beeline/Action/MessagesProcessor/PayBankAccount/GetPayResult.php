<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 09.07.19
 * Time: 17:56
 */

namespace Pyrobyte\SmsPayments\Payment\Beeline\Action\MessagesProcessor\PayBankAccount;

use Pyrobyte\SmsPayments\Services\MessagesProcessor\MessagesProcessorStateAbstract;

class GetPayResult extends MessagesProcessorStateAbstract
{
    protected $name = 'Получение сообщения отправки платежа';

    public function doProcess($message)
    {
        /*
         * Платёж ******* на сумму **.** р. Код перевода *******. Комиссия **.** р. за оплату Альфа-Банк (online).
         */
        $paymentSucceed = (bool)preg_match('/Платёж.\d+.на сумму/imu', $message->get());
        return $paymentSucceed ? true : null;
    }
}