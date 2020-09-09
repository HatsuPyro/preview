<?php


namespace Pyrobyte\SmsPayments\Result;


abstract class GetAuthSmsAbstract extends ResultAbstract
{
    private $authSms = [];

    public function __construct($authSms)
    {
        $this->authSms = $authSms;
    }

    /**
     * Получает смс авторизации.
     * @return array
     */
    public function getAuthSms(): array
    {
        return $this->authSms;
    }
}