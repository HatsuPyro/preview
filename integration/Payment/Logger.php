<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.11.2018
 * Time: 13:48
 */

namespace App\Extensions\Payment;

/**
 * Класс логгера
 * Class Logger
 * @package App\Extensions\Payment
 */
class Logger extends \Pyrobyte\Logger\Logger
{
    const LEVEL_MESSAGES = 'messages';
    const TYPE_UNKNOWN_RESPONSE = 'unknown_response';
}