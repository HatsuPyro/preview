<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 23.10.2018
 * Time: 12:56
 */

namespace App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions;

use Pyrobyte\SmsPayments\Payment\Qiwi\Action\PayWallet as QiwiPayWalletAction;

/**
 * Адаптер вывода с киви на другой кошелек
 * Class PayWallet
 * @package App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions
 */
class PayWallet extends PayActionAbstract
{
    private $wallet = null;
    private $sum = null;

    public function __construct($wallet, $sum)
    {
        $this->wallet = $wallet;
        $this->sum = $sum;
    }

    protected function getClientResult()
    {
        $result = $this->client->call(new QiwiPayWalletAction($this->wallet, $this->sum));

        return $result;
    }
}