<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/15/19
 * Time: 4:30 PM
 */

namespace Pyrobyte\ApiPayments\Payment\Adgroup\Result;


use Pyrobyte\ApiPayments\Result\ResultAbstract;

/**
 * Class GetWallets
 * @package Pyrobyte\ApiPayments\Payment\AdgroupQiwi\Result
 */
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
    public function getWallets(): array
    {
        return $this->wallets;
    }


}