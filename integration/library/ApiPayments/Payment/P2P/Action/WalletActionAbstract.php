<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 18.11.19
 * Time: 15:28
 */

namespace Pyrobyte\ApiPayments\Payment\P2P\Action;


abstract class WalletActionAbstract  extends ActionAbstract
{
    protected $hash = null;

    public function __construct($apiKey, $hash)
    {
        parent::__construct($apiKey);
        $this->hash = $hash;
    }
    abstract protected function setUrl();
}