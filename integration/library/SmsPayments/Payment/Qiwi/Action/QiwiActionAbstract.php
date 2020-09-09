<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 18.10.2018
 * Time: 15:53
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action;


use Pyrobyte\SmsPayments\Action\MessageProcessingAction;
use Pyrobyte\SmsPayments\Config;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\ErrorChecker\QiwiErrorChecker;
use Pyrobyte\SmsPayments\Services\MessagesProcessor\MessagesProcessor;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\SendVerificationMessage;

abstract class QiwiActionAbstract extends MessageProcessingAction
{

    protected $resultClass = null;
    protected $steps = [
        SendVerificationMessage::class,
    ];

    protected function initConfig()
    {
        parent::initConfig();
        $this->to = $this->config['qiwi_phone'];
    }

    protected function initMessageProcessor()
    {
        parent::initMessageProcessor();
        $this->messagesProcessor->setMessageChecker(new QiwiErrorChecker($this->engine));
    }
}