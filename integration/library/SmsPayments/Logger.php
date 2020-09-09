<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.11.2018
 * Time: 13:32
 */

namespace Pyrobyte\SmsPayments;


class Logger extends \Pyrobyte\Logger\Logger
{
    const LEVEL_MESSAGES = 'messages';

    public function addIncomeSms($message)
    {
        $this->addMessage('Message added: ' . $message, Logger::LEVEL_MESSAGES);
        return $this;
    }
}