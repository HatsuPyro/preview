<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/28/19
 * Time: 10:54 AM
 */

namespace App\Extensions\Payment\Adapter\Qiwi;


use App\Extensions\Payment\Adapter\AdgroupCardApi;

class QiwiAdgroupCardApi extends AdgroupCardApi
{
    protected $provider = 'QIWI';
}