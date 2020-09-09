<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 09.07.19
 * Time: 15:02
 */

namespace Pyrobyte\SmsPayments\Payment\Beeline\Action;

use Pyrobyte\SmsPayments\Action\MessageProcessingAction;
use Pyrobyte\SmsPayments\Config;
use Pyrobyte\SmsPayments\Payment\Beeline\Action\MessagesProcessor\PayBankAccount\SendVerificationMessage;
use Pyrobyte\SmsPayments\Payment\Beeline\Action\MessagesProcessor\PayBankAccount\GetPayResult;
use Pyrobyte\SmsPayments\Payment\Beeline\Action\MessagesProcessor\ErrorChecker\PayChecker;

class PayBankAccount extends MessageProcessingAction
{
    private $bankAccount = null;
    private $sum = null;
    protected $steps = [
        SendVerificationMessage::class,
        GetPayResult::class,
    ];
    protected $resultClass = \Pyrobyte\SmsPayments\Payment\Beeline\Result\PayBankAccount::class;

    protected function initResponseTime()
    {
        $this->responseTime = Config::getItem('beeline.payout.time');
    }

    public function __construct($bankAccount, $sum)
    {
        parent::__construct();

        $this->bankAccount = $bankAccount;
        $this->sum = $sum;
    }

    protected function initConfig()
    {
        parent::initConfig();
        $this->to = $this->config['service_ruru_phone'];
        $this->from = Config::getItem('beeline.payout.from_phones');
    }

    protected function getMessage()
    {
        return 'alfa ' . $this->bankAccount . ' ' . $this->sum;
    }

    protected function initMessageProcessor()
    {
        parent::initMessageProcessor();
        $this->messagesProcessor->setMessageChecker(new PayChecker($this->engine));
    }

}