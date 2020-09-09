<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/15/19
 * Time: 5:19 PM
 */

namespace App\Extensions\Payment\Adapter\Result;


use App\Extensions\Payment\Entities\Wallet;

class GetWallets extends ResultAbstract
{
    private $wallets = [];

    public function __construct($status, $wallets)
    {
        parent::__construct($status);
        $this->wallets = $wallets;
    }

    /**
     * @return Wallet[]
     */
    public function getWallets(): array
    {
        return $this->wallets;
    }
}