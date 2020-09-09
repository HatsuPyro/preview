<?php


namespace Pyrobyte\SmsPayments\Action;

abstract class GetAuthSmsAbstract extends ActionAbstract
{
    protected $simboxId;
    protected $resultClass = null;

    abstract public function getAuthPatterns();

    public function do() {
        $smsMessages = $this->engine->getSms();
        $authPattern = $this->getAuthPatterns();
        $filterSms = [];

        foreach($smsMessages as $sms) {
            if (preg_match($authPattern['pattern'], $sms->get())) {
                $filterSms[] = $sms;
            }
        }

        return new $this->resultClass($filterSms);
    }
}