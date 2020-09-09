<?php

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action;

use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\SendVerificationMessage;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\Pay\GetPayResult;


class PaySelfPhone extends PayAbstract
{
    protected $resultClass = \Pyrobyte\SmsPayments\Payment\Qiwi\Result\PaySelfPhone::class;

    protected $steps = [
        SendVerificationMessage::class,
        GetPayResult::class,
    ];

    public function __construct($sum)
    {
        parent::__construct();
        $this->sum = $sum;
    }

    protected function getMessage()
    {
        return $this->sum;
    }
}