<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 18.11.19
 * Time: 15:44
 */

namespace Pyrobyte\ApiPayments\Payment\P2P\Result;

use Pyrobyte\ApiPayments\Exceptions\InvalidResponseException;
use Pyrobyte\ApiPayments\Result\ResultAbstract;

class GetBalance extends ResultAbstract
{
    private $balance = null;

    public function __construct($balance)
    {
        $this->balance = $balance;
    }

    /**
     * Get balance
     * @return null
     */
    public function getBalance()
    {
        if(!isset($this->balance->balance_rub)) {
            throw new InvalidResponseException('При запросе данных баланса кошелька системы P2P, не был получен баланс');
        }
        return $this->balance->balance_rub;
    }
}