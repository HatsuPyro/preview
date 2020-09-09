<?php

/**
 *
 */

namespace Pyrobyte\WebPayments\Payment;

abstract class ActionAbstract
{
    protected $engine;
    protected $client;

    /**
     * Выполнение задачи. Возвращает объект Pyrobyte\WebPayments\Payment\Result
     * @return mixed
     */
    abstract public function run();

    /**
     * Выполнение запроса через указанный движок парсера
     *
     * @param $request
     * @return mixed
     */
    protected function request($request)
    {
        $response = $this->engine->request($request);
        $this->engine->getCookies()->save($this->engine->getCookiesFile());
        return $response;
    }

    public function setEngine(\Pyrobyte\WebPayments\EngineAbstract $engine)
    {
        $this->engine = $engine;
    }

    public function setClient(\Pyrobyte\WebPayments\Payment\ClientAbstract $client)
    {
        $this->client = $client;
    }
}