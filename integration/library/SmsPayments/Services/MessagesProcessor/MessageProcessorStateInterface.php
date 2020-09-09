<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.10.2018
 * Time: 15:11
 */

namespace Pyrobyte\SmsPayments\Services\MessagesProcessor;


interface MessageProcessorStateInterface
{
    public function doProcess($message);

    public function setEngine($engine);
}