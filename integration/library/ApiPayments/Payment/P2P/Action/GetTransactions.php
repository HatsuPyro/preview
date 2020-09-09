<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 18.11.19
 * Time: 14:28
 */

namespace Pyrobyte\ApiPayments\Payment\P2P\Action;

use \Pyrobyte\ApiPayments\Payment\Adgroup\Result\GetTransactions as Result;

class GetTransactions extends WalletActionAbstract
{
    protected $resultClass = Result::class;

    protected function setUrl()
    {
        $this->url = '/list?api_key=' . $this->apiKey . '&hash=' . $this->hash;
    }
}