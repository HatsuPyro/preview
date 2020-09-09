<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 1/21/19
 * Time: 4:20 PM
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action;


use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\CheckPay\GetResultMessage;
use Pyrobyte\SmsPayments\Services\MessagesProcessor\FixedTimer;

class CheckPay extends QiwiActionAbstract
{
    // Время, в которое пришло сообщение об отправке средств
    private $time = null;
    protected $needSendSms = false;
    private $sum = null;
    protected $resultClass = \Pyrobyte\SmsPayments\Payment\Qiwi\Result\CheckPay::class;
    protected $responseTime = 5;

    public function __construct($time, $sum)
    {
        parent::__construct();
        $this->time = $time;
        $this->sum = $sum;
        $this->steps = [
            new GetResultMessage($sum),
        ];
    }

    protected function initMessageProcessor()
    {
        parent::initMessageProcessor();
        $this->messagesProcessor->setTimer(new FixedTimer($this->time));
        $this->messagesProcessor->setParseFrom('QIWIWallet');
    }
}