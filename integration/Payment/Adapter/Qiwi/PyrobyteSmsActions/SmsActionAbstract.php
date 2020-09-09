<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.10.2018
 * Time: 10:38
 */

namespace App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions;


abstract class SmsActionAbstract
{
    protected $client = null;

    /**
     * Задает клиент
     * @param $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    abstract public function do();
}