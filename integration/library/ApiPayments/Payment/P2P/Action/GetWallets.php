<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 18.11.19
 * Time: 13:18
 */

namespace Pyrobyte\ApiPayments\Payment\P2P\Action;

use \Pyrobyte\ApiPayments\Payment\P2P\Result\GetWallets as Result;

class GetWallets extends ActionAbstract
{
    protected $resultClass = Result::class;

    protected function setUrl()
    {
        $this->url = '/wallets-list?api_key=' . $this->apiKey;
    }

}