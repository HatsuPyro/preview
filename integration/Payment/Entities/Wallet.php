<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/15/19
 * Time: 5:59 PM
 */

namespace App\Extensions\Payment\Entities;


/**
 * Class Wallet
 * @package App\Extensions\Payment\Entities
 */
class Wallet
{
    private $number = null;
    private $balance = null;
    private $specificData = [];

    /**
     * Wallet constructor.
     * @param null $number
     * @param null $walletName
     */
    public function __construct($number, $balance = null)
    {
        $this->number = $number;
        $this->balance = $balance;
    }

    /**
     * Задать специфические для пс данные
     * @param array $data
     */
    public function setSpecificData(array $data)
    {
        $this->specificData = $data;
    }

    /**
     * Получить специфические для пс данные
     * @return array
     */
    public function getSpecificData()
    {
        return $this->specificData;
    }

    /**
     * Получает элемент специфических данных по ключу
     * @param $key
     * @return mixed|null
     */
    public function getSpecificDataItem($key)
    {
        return $this->getSpecificData()[$key] ?? null;
    }

    /**
     * Получить номер кошелька
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Получить баланс кошелька
     * @return float|null
     */
    public function getBalance()
    {
        return $this->balance;
    }
}