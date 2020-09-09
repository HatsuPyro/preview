<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 1/21/19
 * Time: 6:04 PM
 */

namespace Pyrobyte\SmsPayments\Services\MessagesProcessor;


/**
 * Неизменяемый таймер
 * Class FixedTimer
 * @package Pyrobyte\SmsPayments\Services\MessagesProcessor
 */
class FixedTimer extends Timer
{
    public function __construct($time)
    {
        $this->times[] = $time;
    }

    public function refresh()
    {
        return $this;
    }
}