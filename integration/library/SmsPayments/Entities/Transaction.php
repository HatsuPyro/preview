<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/8/19
 * Time: 4:35 PM
 */

namespace Pyrobyte\SmsPayments\Entities;


class Transaction
{
    const TYPE_INCOME = 'income';
    const TYPE_OUTCOME = 'outcome';
    private $type = null;
    private $sum = null;
    private $message = null;
    private $date = null;
    private $payer = null;
    private $dest = null;
    private $balance = null;
    private $comission = null;
    private $stateLock = null;
    private $card_number = null;

    /**
     * Transaction constructor.
     * @param string $type
     * @param float $sum
     * @param string $message
     * @param int $date
     */
    public function __construct(string $type, float $sum, string $message, int $date)
    {
        $this->type = $type;
        $this->sum = $sum;
        $this->message = $message;
        $this->date = $date;
    }

    /**
     * Задает источник транзакции(откуда идут деньги)
     * @param string $source
     */
    public function setPayer($payer): void
    {
        $this->payer = $payer;
    }

    public function setCardNumber($cardNumber)
    {
        $this->card_number = $cardNumber;
    }

    public function getCardNumber()
    {
        return $this->card_number;
    }


    /**
     * Задает источник транзакции(откуда идут деньги)
     * @param string $source
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * Задает пункт назначения перевода средств
     * @param string $dest
     */
    public function setDest($dest): void
    {
        $this->dest = $dest;
    }

    /**
     * Задаеи баланс
     * @param float $balance
     */
    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    public function setStateLock($stateLock)
    {
        $this->stateLock = $stateLock;
    }

    public function getStateLock()
    {
        return $this->stateLock;
    }


    /**
     * Получает тип транзакции
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Получает сумму транзакции
     * @return float
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * Получает исходное сообщение транзакции
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Получает дату проведения транзакции в timestamp
     * @return int
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Получает источник транзакции(откуда идут деньги)
     * @return string
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * Получает пункт назначения перевода средств
     * @return string
     */
    public function getDest()
    {
        return $this->dest;
    }

    /**
     * Получает баланс на момент проведения транзакции
     * @return float|null
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Получает коммиссию транзакции
     * @return null
     */
    public function getComission()
    {
        return $this->comission;
    }

    /**
     * Задает коммиссию транзакции
     * @param null $comission
     */
    public function setComission($comission): void
    {
        $this->comission = $comission;
    }
}