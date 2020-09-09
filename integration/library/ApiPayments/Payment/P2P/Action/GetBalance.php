<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 18.11.19
 * Time: 15:44
 */

namespace Pyrobyte\ApiPayments\Payment\P2P\Action;

use \Pyrobyte\ApiPayments\Payment\P2P\Result\GetBalance as Result;

class GetBalance extends WalletActionAbstract
{
    protected $resultClass = Result::class;

    protected function setUrl()
    {
        $this->url = '/balance?api_key=' . $this->apiKey . '&hash=' . $this->hash;
    }
}