<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 09.10.2018
 * Time: 10:40
 */

namespace Pyrobyte\Sesame\Result\Entities;

/**
 * Класс-обертка для номера телефона
 * Class Phone
 * @package Pyrobyte\Sesame\Result
 */
class Phone
{
    public $id = null;
    public $number = null;
    public $connected = null;
    private $iccid = null;
    private $provider = null;
    private $active = null;
    private $channel = null;

    public function __construct($sesameObj)
    {
        $this->id = $sesameObj->id;
        $this->number = $sesameObj->number;
        $this->connected = $sesameObj->active;
        $this->iccid = $sesameObj->iccid;
        $this->provider = $sesameObj->provider;
        $this->active = $sesameObj->active;
        $this->channel = $sesameObj->channel;
    }

    /**
     * Получает id телефона в системе сезам
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Получает статус подключения номера
     * @return null
     */
    public function getConnectionStatus()
    {
        return $this->connected;
    }

    /**
     * Получает номер телефона
     * @return null
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Получает провайдера
     * @return null
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Получает уникальный ид сим-карты у провайдера
     * @return null
     */
    public function getIccid()
    {
        return $this->iccid;
    }

    /**
     * Активен ли телефон
     * @return bool
     */
    public function isActive()
    {
        return (bool)$this->active;
    }

    /**
     * Получает канал у сим-карты
     * @return null
     */
    public function getChannel()
    {
        return $this->channel;
    }
}