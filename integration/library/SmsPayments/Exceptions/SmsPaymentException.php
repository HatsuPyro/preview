<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.10.2018
 * Time: 14:41
 */

namespace Pyrobyte\SmsPayments\Exceptions;


class SmsPaymentException extends \Exception
{
    public function setMessage($message) {
        $this->message = $message;
    }
}