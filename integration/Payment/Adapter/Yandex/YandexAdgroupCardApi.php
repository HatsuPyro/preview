<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 15.11.19
 * Time: 19:36
 */

namespace App\Extensions\Payment\Adapter\Yandex;

use App\Extensions\Payment\Adapter\AdgroupCardApi;

class YandexAdgroupCardApi extends AdgroupCardApi
{
    protected $provider = 'YANDEX';
}