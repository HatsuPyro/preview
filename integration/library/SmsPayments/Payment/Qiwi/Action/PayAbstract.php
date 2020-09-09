<?php
/**
 * Created by https://github.com/Wheiss
 * Date: 11.11.2018
 * Time: 0:27
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action;

use Pyrobyte\SmsPayments\Config;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\ErrorChecker\PayChecker;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\Pay\GetPayResult;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\Pay\SendSecondVerificationMessage;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\SendVerificationMessage;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\Pay\SendCodeMessage;

abstract class PayAbstract extends QiwiActionAbstract
{
    protected $steps = [
        SendVerificationMessage::class,
        SendCodeMessage::class,
        SendSecondVerificationMessage::class,
        GetPayResult::class,
    ];
    protected $sum = null;

    protected function initResponseTime()
    {
        $this->responseTime = Config::getItem('qiwi.payout.time');
    }

    protected function initMessageProcessor()
    {
        parent::initMessageProcessor();
        $this->messagesProcessor->setMessageChecker(new PayChecker($this->engine));
    }
}