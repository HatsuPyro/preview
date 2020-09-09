<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 19.11.19
 * Time: 19:27
 */

namespace Pyrobyte\ApiPayments\Payment\P2P\Action;

use Pyrobyte\ApiPayments\Payment\P2P\Result\UpdateWallets as Result;

class UpdateWallets extends ActionAbstract
{
    protected $resultClass = Result::class;

    protected function setUrl()
    {
        $this->url = '/refresh-all-wallets?api_key=' . $this->apiKey;
    }
}