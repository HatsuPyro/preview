<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 1/21/19
 * Time: 5:50 PM
 */

namespace Pyrobyte\SmsPayments\Services\MessagesProcessor;


class Timer
{
    protected  $times = [];

    public function __construct()
    {
        $this->times[] = time();
    }

    /**
     * Обновление таймера
     * @return $this
     */
    public function refresh()
    {
        $this->times[] = time();
        return $this;
    }

    /**
     * Получает время старта
     * @return int|null
     */
    public function getTime()
    {
        return array_last($this->times);
    }

    /**
     * Получает кол-во секунд, прошедших с начала отсчета
     * @return int|null
     */
    public function getSecondsPassed()
    {
        return time() - $this->getTime();
    }
}