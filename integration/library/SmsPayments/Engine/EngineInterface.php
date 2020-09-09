<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.10.2018
 * Time: 15:07
 */

namespace Pyrobyte\SmsPayments\Engine;


interface EngineInterface
{
    public function getSms();

    public function sendSms($to, $message);
}