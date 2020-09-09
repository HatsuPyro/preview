<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 25.07.19
 * Time: 16:53
 */

namespace App\Extensions\Payment\Adapter\Mkb;

use App\Extensions\Payment\Adapter\SmsPaymentAbstract;
use Pyrobyte\SmsPayments\Payment\Mkb\Action\GetTransactions;
use Pyrobyte\SmsPayments\Payment\Mkb\Client;

class MkbSms extends SmsPaymentAbstract
{
    protected $getTransactionsActionClass = GetTransactions::class;
    protected $clientClass = Client::class;

}