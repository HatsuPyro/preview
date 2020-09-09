<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/15/19
 * Time: 5:53 PM
 */

namespace Pyrobyte\ApiPayments\Entities;


class Wallet
{
    private $number = null;
    private $balance = null;
    private $userId = null;
    private $cardNumber = null;
    private $name = null;

    /**
     * Wallet constructor.
     * @param null $number
     * @param $user_id
     * @param null $balance
     */
    public function __construct($number = null, $userId = null, $balance = null)
    {
        $this->number = $number;
        $this->balance = $balance;
        $this->userId = $userId;
    }

    /**
     * Set nimber
     * @param null $number
     */
    public function setNumber($number): void
    {
        $this->number = $number;
    }

    /**
     * Set balance
     * @param null $balance
     */
    public function setBalance($balance): void
    {
        $this->balance = $balance;
    }

    /**
     * Set user id
     * @param null $userId
     */
    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Get card number
     * @return null
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * Set card number
     * @param null $cardNumber
     * @return $this
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    /**
     * Get wallet name
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @param null $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get wallet number
     * @return null
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Get wallet balance
     * @return null
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Get user id
     * @return null
     */
    public function getUserId()
    {
        return $this->userId;
    }

}