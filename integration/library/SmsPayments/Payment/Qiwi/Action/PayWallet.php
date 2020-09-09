<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 23.10.2018
 * Time: 12:59
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action;

use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\Pay\GetPayResult;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\Pay\SendCodeMessage;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\SendVerificationMessage;
use Pyrobyte\SmsPayments\Payment\Qiwi\Result\PayWallet as PayWalletResult;

class PayWallet extends PayAbstract
{
    private $wallet = null;
    protected $resultClass = PayWalletResult::class;
    protected $message = 'perevod';


    public function __construct($wallet, $sum)
    {
        parent::__construct();
        $this->wallet = $wallet;
        $this->sum = $sum;
    }

    public function getMessage()
    {
        return $this->message . ' ' . $this->wallet . ' ' . $this->sum;
    }
}