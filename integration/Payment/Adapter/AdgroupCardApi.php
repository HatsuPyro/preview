<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 15.11.19
 * Time: 19:01
 */

namespace App\Extensions\Payment\Adapter;

use Pyrobyte\ApiPayments\Payment\Adgroup\Action\GetCardWallets;
use Pyrobyte\ApiPayments\Payment\Adgroup\Action\TransactionFilter\FilterProtocol;

class AdgroupCardApi extends AdgroupAbstract
{
    protected function getWalletsAction()
    {
        return new GetCardWallets($this->provider);
    }

    protected $filterProtocol = FilterProtocol::CARD;
}