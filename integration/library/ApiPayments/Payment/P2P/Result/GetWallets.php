<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 18.11.19
 * Time: 13:18
 */

namespace Pyrobyte\ApiPayments\Payment\P2P\Result;

use Pyrobyte\ApiPayments\Result\ResultAbstract;

class GetWallets extends ResultAbstract
{
    private $wallets = [];

    public function __construct($wallets)
    {
        $this->wallets = $wallets;
    }

    /**
     * get Wallets
     * @return array
     */
    public function getWallets()
    {
        return $this->wallets;
    }
}