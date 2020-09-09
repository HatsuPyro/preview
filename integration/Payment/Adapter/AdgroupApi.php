<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 15.11.19
 * Time: 18:47
 */

namespace App\Extensions\Payment\Adapter;

use Pyrobyte\ApiPayments\Payment\Adgroup\Action\TransactionFilter\FilterProtocol;
use Pyrobyte\ApiPayments\Payment\Adgroup\Action\GetWallets;

class AdgroupApi extends AdgroupAbstract
{
    protected function getWalletsAction()
    {
        return new GetWallets($this->provider);
    }

    protected $filterProtocol = FilterProtocol::WALLET;

}