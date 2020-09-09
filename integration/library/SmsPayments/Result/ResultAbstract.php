<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.10.2018
 * Time: 14:53
 */

namespace Pyrobyte\SmsPayments\Result;


class ResultAbstract
{
    private $time = null;

    public function __construct($response)
    {
        $this->time = $response->getTime();
    }

    /**
     * Получение времени получения ответа
     * @return int | null
     */
    public function getTime()
    {
        return $this->time;
    }
}