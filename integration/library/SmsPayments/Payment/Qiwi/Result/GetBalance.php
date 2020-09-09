<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.10.2018
 * Time: 16:06
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Result;


use Pyrobyte\SmsPayments\Result\GetBalanceAbstract;

class GetBalance extends GetBalanceAbstract
{
    public function __construct($result)
    {
        parent::__construct($result->getResult());
    }
}