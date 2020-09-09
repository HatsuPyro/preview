<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.10.2018
 * Time: 12:47
 */

namespace Pyrobyte\SmsPayments\Engine;


use Pyrobyte\Phone\ServiceFactory;
use Pyrobyte\SmsPayments\Logger;

class PhoneService implements EngineInterface
{
    private $service = null;
    private $id = null;

    public function __construct($id)
    {
        $this->id = $id;
        $factory = new ServiceFactory();
        $this->service = $factory->getService();
    }

    public function getSms()
    {
        return $this->service->getSms([$this->id])->getMessages();
    }

    public function sendSms($to, $message)
    {
        $logger = Logger::getInstance();
        $sendSmsResult = $this->service->sendSms([$this->id], $to, $message);
        $logger->addMessage('Sms sent: to(' . $to . '), message(' . $message . ')' . ', request_id(' . $sendSmsResult->getRequestId() . ')', Logger::LEVEL_MESSAGES);
        return $sendSmsResult;
    }

    public function sendUssd($id, $message)
    {
        $sendUssdResult = $this->service->sendUssd($id, $message);
        return $sendUssdResult;
    }

    public function getBalance($simId)
    {
        return $this->service->getBalance($simId);
    }

    public function getUssdResult($requestId)
    {
        return $this->service->getUssdResult($requestId);
    }
}