<?php

/**
 * Created by nikita@hotbrains.ru
 * Date: 20.09.2018
 * Time: 18:08
 */

namespace App\Extensions\Payment;

/**
 * Класс транзакций
 * Class Transaction
 * @package App\Extensions\Payment
 */
class Transaction implements TransactionInterface
{
    public $transaction_id = null;
    public $date = null;
    public $sum_amount = null;
    public $sum_currency = null;
    public $type = null;
    public $description = null;
    public $payDate = null;
    public $status = null;
    public $response = null;
    public $payer = null;
    public $destination = null;
    public $balance = null;
    public $state = null;
    public $card_number = null;
    public $comission_amount = null;
    public $comission_currency = null;


    public function __construct($properties = [])
    {
        $fields = get_class_vars(self::class);
        foreach ($fields as $key => $defaultValue) {
            // На данный момент все свойства класса инициализируются null, если это изменится - стоит изменить логику
            $this->{$key} = $properties[$key] ?? $defaultValue;
        }
    }

    /**
     * Получается описание транзакции
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Возвращает тип транзакции (income|outcome)
     * @return null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Возвращает сумму транзакции
     * @return null
     */
    public function getSumAmount()
    {
        return $this->sum_amount;
    }

    /**
     * Возвращает дату транзакции
     * @return null
     */
    public function getDate()
    {
        return $this->date;
    }

    public function getState()
    {
        return $this->state;
    }

    /**
     * Получает источник транзакции(откуда переводятся деньги)
     * @return null
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * Получает направление транзакции(куда переводятся деньги)
     * @return null
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @return null
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Задан ли в транзакции баланс
     * @return bool
     */
    public function hasBalance()
    {
        return $this->balance !== null;
    }
}