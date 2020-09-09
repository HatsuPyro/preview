<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 09.07.19
 * Time: 17:09
 */

namespace Pyrobyte\SmsPayments\Payment\Beeline\Action\MessagesProcessor\PayBankAccount;

use Pyrobyte\SmsPayments\Config;
use Pyrobyte\SmsPayments\Services\MessagesProcessor\MessagesProcessorStateAbstract;

class SendVerificationMessage extends MessagesProcessorStateAbstract
{
    /*
     * Для подтверждения оплаты на (20 чисел) по цене **,**, отправте ответное бесп. SMS с цифрой 1, для отказа - 0.
     */
    protected $verificationDemandPattern = '/(Для подтверждения оплаты)/imu'; // Для смс другой формат сообщения
    protected $verificationMessage = '1';
    protected $name = 'Сообщение подтверждения';

    public function doProcess($message)
    {
        $needSend = preg_match($this->verificationDemandPattern, $message->get());
        if (!$needSend) {
            return null;
        }
        $to = Config::getItem('beeline_phone_pay_confirmation');
        $this->engine->sendSms($to, $this->verificationMessage);
        return true;
    }

}