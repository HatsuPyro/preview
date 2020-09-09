<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 08.07.19
 * Time: 16:52
 */

namespace App\Extensions\Payment\Adapter\Beeline;

use App\Extensions\Payment\Adapter\ErrorCatcher\SmsErrorCatcher;
use Pyrobyte\SmsPayments\Payment\Beeline\Client;
use Pyrobyte\SmsPayments\Payment\Beeline\Action\PayBankAccount;
use App\Extensions\Payment\PaymentAbstract;
use App\Extensions\Payment\Adapter\Result\PayResult;
use App\Extensions\Payment\Adapter\Result\PayConfirmResult;
use Pyrobyte\SmsPayments\Payment\Beeline\Action\CheckPay;
use App\Extensions\Payment\Adapter\SmsPaymentAbstract;
use Pyrobyte\SmsPayments\Payment\Beeline\Action\GetTransactions;

class BeelineSms extends SmsPaymentAbstract
{
    protected $getTransactionsActionClass = GetTransactions::class;
    protected $clientClass = Client::class;

    /**
     * @inheritdoc
     */
    public function payBankAccount($bankAccount, $sum)
    {
        $result = $this->doAction(new PayBankAccount($bankAccount, $sum));
        return new PayResult($result->getStatus(), $result->getTime());
    }

    /**
     * @inheritdoc
     */
    public function getPayConfirm($time, $sum)
    {
        $result = $this->doAction(new CheckPay($time, $sum));
        return new PayConfirmResult($result->getStatus());
    }
}