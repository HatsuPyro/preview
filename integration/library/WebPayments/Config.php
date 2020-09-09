<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/17/19
 * Time: 4:40 PM
 */

namespace Pyrobyte\WebPayments;


use Pyrobyte\Config\PyrobyteConfig;

class Config extends PyrobyteConfig
{
    protected static $config = [
        'guzzle' => [
            'connect_timeout' => 15,
        ],
    ];

    protected static $defferedConfig = [];
}