<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/14/19
 * Time: 4:00 PM
 */

namespace App\Extensions\Payment\Adapter\Qiwi;

use App\Extensions\Payment\Adapter\AdgroupApi;

class QiwiAdgroupApi extends AdgroupApi
{
    protected $provider = 'QIWI';
}