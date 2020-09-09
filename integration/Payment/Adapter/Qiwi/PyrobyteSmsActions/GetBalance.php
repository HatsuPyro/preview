<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.10.2018
 * Time: 10:39
 */

namespace App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions;

use \Pyrobyte\SmsPayments\Payment\Qiwi\Action\GetBalance as QiwiAction;

/**
 * Адаптер обновления баланса кошелька для киви смс
 * Class GetPayConfirm
 * @package App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions
 */
class GetBalance extends SmsActionAbstract
{

    public function do()
    {
        $result = $this->client->call(new QiwiAction());

        return $result->getBalance();
    }
}