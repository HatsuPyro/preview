<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 09.07.19
 * Time: 18:50
 */

namespace Pyrobyte\SmsPayments\Payment\Beeline\Action;

use Pyrobyte\SmsPayments\Action\MessageProcessingAction;
use Pyrobyte\SmsPayments\Config;
use Pyrobyte\SmsPayments\Payment\Beeline\Action\MessagesProcessor\CheckPay\GetResultMessage;
use Pyrobyte\SmsPayments\Services\MessagesProcessor\FixedTimer;
use Pyrobyte\SmsPayments\Payment\Beeline\Action\MessagesProcessor\ErrorChecker\PayChecker;

class CheckPay extends MessageProcessingAction
{
    // Время, в которое пришло сообщение об отправке средств
    private $time = null;
    protected $needSendSms = false;
    private $sum = null;
    protected $resultClass = \Pyrobyte\SmsPayments\Payment\Beeline\Result\CheckPay::class;
    protected $responseTime = 5;

    public function __construct($time, $sum)
    {
        parent::__construct();
        $this->time = $time;
        $this->sum = $sum;
        $this->steps = [
            new GetResultMessage(),
        ];
    }

    protected function initMessageProcessor()
    {
        parent::initMessageProcessor();
        $this->messagesProcessor->setTimer(new FixedTimer($this->time));
        $this->messagesProcessor->setParseFrom(Config::getItem('service_ruru_phone'));
        $this->messagesProcessor->setMessageChecker(new PayChecker($this->engine));
    }

}