<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 15.11.19
 * Time: 19:34
 */

namespace App\Extensions\Payment\Adapter\Yandex;

use App\Extensions\Payment\Adapter\AdgroupApi;

class YandexAdgroupApi  extends AdgroupApi
{
    protected $provider = 'YANDEX';
}