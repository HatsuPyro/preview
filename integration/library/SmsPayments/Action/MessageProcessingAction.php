<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 10.12.2018
 * Time: 11:28
 */

namespace Pyrobyte\SmsPayments\Action;


use Pyrobyte\SmsPayments\Config;
use Pyrobyte\SmsPayments\Exceptions\GetSmsTimeoutException;
use Pyrobyte\SmsPayments\Services\MessagesProcessor\MessagesProcessor;

abstract class MessageProcessingAction extends ActionAbstract
{
    protected $steps = [];
    // Время на запросы в секундах
    protected $responseTime = 90;
    protected $startTime;
    protected $to = null;
    protected $message = null;
    protected $config = [];
    protected $resultClass = null;
    protected $provider = null;
    protected $needSendSms = true;
    protected $from = null;

    /**
     * @var MessagesProcessor
     */
    protected $messagesProcessor = null;

    protected function getMessage()
    {
        return $this->message;
    }

    public function __construct()
    {
        $this->initConfig();
    }

    protected function initConfig()
    {
        $this->config = Config::getConfig();
        $this->initResponseTime();
    }

    protected function initResponseTime() {    }

    protected function initMessageProcessor()
    {
        $this->messagesProcessor = new MessagesProcessor($this->engine, $this->steps, $this->responseTime, $this->provider);
        $this->messagesProcessor->setParseFrom($this->from ?? $this->to);
    }

    public function do()
    {
        $this->initMessageProcessor();

        $this->startTime = time();
        if($this->needSendSms) {
            $this->engine->sendSms($this->to, $this->getMessage());
        }

        $result = null;

        $result = $this->messagesProcessor->processMessages();

        $resultClass = $this->resultClass;
        return new $resultClass($result);
    }
}