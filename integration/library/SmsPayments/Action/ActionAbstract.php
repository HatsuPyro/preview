<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.10.2018
 * Time: 14:51
 */

namespace Pyrobyte\SmsPayments\Action;


use Pyrobyte\SmsPayments\Engine\EngineInterface;

abstract class ActionAbstract
{
    /**
     * @var EngineInterface
     */
    protected $engine = null;
    protected $provider = null;
    protected $currency = null;
    protected $cardNumber = null;


    public function setEngine(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param null $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
    }

    abstract function do();
}