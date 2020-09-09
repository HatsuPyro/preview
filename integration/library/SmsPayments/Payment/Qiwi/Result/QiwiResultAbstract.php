<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 23.10.2018
 * Time: 13:00
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Result;


use Pyrobyte\SmsPayments\Result\ResultAbstract;

abstract class QiwiResultAbstract extends ResultAbstract
{
    private $response = null;

    public function __construct($response)
    {
        parent::__construct($response);
        $this->response = $response->getResult();
    }

    /**
     * Выполнилась ли операция
     * @return bool
     */
    public function getStatus()
    {
        return (bool)$this->response;
    }
}