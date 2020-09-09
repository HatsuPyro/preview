<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/2/19
 * Time: 1:18 PM
 */

namespace Pyrobyte\Sesame;


use Pyrobyte\Config\PyrobyteConfig;
use Pyrobyte\Sesame\Action\GetBalance;

class Config extends PyrobyteConfig
{
    protected static $config = [
        'api_key' => null,
        'base_uri' => 'http://190.2.133.53:8080',
        'get_balance_type' => GetBalance::TYPE_USSD,
    ];
    protected static $errorMap = [
        'api_key' => 'Не задан ключ api для Sesame',
        'get_balance_type' => 'Не задан тип получения сим-баланса',
        'base_uri' => 'Не задан базовый uri',
    ];
}