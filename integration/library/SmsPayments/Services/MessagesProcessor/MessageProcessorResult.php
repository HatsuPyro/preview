<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 1/17/19
 * Time: 2:03 PM
 */

namespace Pyrobyte\SmsPayments\Services\MessagesProcessor;


class MessageProcessorResult
{
    private $result = null;
    private $time = null;

    /**
     * @param string $result
     * @return $this
     */
    public function setResult($result): self
    {
        $this->result = $result;
        return $this;
    }

    /**
     * @param int $time
     * @return $this
     */
    public function setTime($time): self
    {
        $this->time = $time;
        return $this;
    }

    /**
     * Результат выполнения
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Время получения результата выполнения
     * @return int|null
     */
    public function getTime(): ? int
    {
        return $this->time;
    }


}