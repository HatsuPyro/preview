<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.10.2018
 * Time: 14:49
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action;


use Pyrobyte\SmsPayments\Config;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\GetBalance\GetBalanceResult;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\SendVerificationMessage;

class GetBalance extends QiwiActionAbstract
{
    protected $message = 'balance';
    protected $resultClass = \Pyrobyte\SmsPayments\Payment\Qiwi\Result\GetBalance::class;
    protected $steps =[
        SendVerificationMessage::class,
        GetBalanceResult::class,
    ];

    protected function initResponseTime()
    {
        $this->responseTime = Config::getItem('qiwi.balance.time');
    }
}