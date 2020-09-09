<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/14/19
 * Time: 3:59 PM
 */

namespace Pyrobyte\ApiPayments\Payment\Adgroup;

use Pyrobyte\ApiPayments\Payment\Adgroup\Action\GetWallets;
use Pyrobyte\ApiPayments\Payment\ClientAbstract;

/**
 * Class Client
 * @package Pyrobyte\ApiPayments\Payment\AdgroupQiwi
 */
class Client extends ClientAbstract
{
    public $AdgroupClientId = null;
    public $AdgroupClientSecret = null;
    public $provider = null;

    public function __construct($AdgroupClientId, $AdgroupClientSecret, $provider)
    {
        $this->AdgroupClientId = $AdgroupClientId;
        $this->AdgroupClientSecret = $AdgroupClientSecret;
        $this->provider = $provider;
        parent::__construct();
    }

    /**
     * @return null
     */
    public function getAdgroupClientId()
    {
        return $this->AdgroupClientId;
    }

    /**
     * @return null
     */
    public function getAdgroupClientSecret()
    {
        return $this->AdgroupClientSecret;
    }

    /**
     * Get wallets
     * @return mixed
     */
    public function getWallets()
    {
        return $this->call(new GetWallets($this->provider));
    }
}