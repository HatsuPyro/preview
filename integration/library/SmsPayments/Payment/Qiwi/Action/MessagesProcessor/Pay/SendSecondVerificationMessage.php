<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 1/9/19
 * Time: 6:24 PM
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\Pay;


use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\SendVerificationMessage;

class SendSecondVerificationMessage extends SendVerificationMessage
{
    protected $name = 'Второе сообщение подтверждения';
}