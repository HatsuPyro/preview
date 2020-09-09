<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/18/19
 * Time: 6:24 PM
 */

namespace Pyrobyte\ApiPayments\Entities;


class Transaction
{
    const STATUS_APPROVED = 'APPROVED';
    const TYPE_INCOME = 'EXTERNAL-MERCHANT';
    const TYPE_OUTCOME = 'MERCHANT-EXTERNAL';

    private $id = null;
    private $type = null;
    private $status = null;
    private $amount = null;
    private $time = null;
    private $provider = null;
    private $currency = null;
    private $payer = null;
    private $destination = null;
    private $note = null;

    public function __construct($params = [])
    {
        foreach ($params as $field => $value) {
            $this->{$field} = $value;
        }
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return null
     */
    public function getTime()
    {
        return date_create($this->time)->getTimestamp();
    }

    /**
     * @return null
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return null
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return null
     */
    public function getPayer()
    {
        return $this->payer;
    }

    /**
     * @return null
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @return null
     */
    public function getNote()
    {
        return $this->note;
    }
}