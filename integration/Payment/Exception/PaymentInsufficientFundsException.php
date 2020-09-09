<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 29.10.2018
 * Time: 12:06
 */

namespace App\Extensions\Payment\Exception;

/**
 * Класс-исключение недостаточности средств
 * Class PaymentInsufficientFundsException
 * @package App\Extensions\Payment\Exception
 */
class PaymentInsufficientFundsException extends PaymentException
{
    private $balance = null;

    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Проверяет, передан ли баланс
     * @return bool
     */
    public function hasBalance()
    {
        return $this->balance !== null;
    }
}