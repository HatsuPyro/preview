<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/15/19
 * Time: 3:46 PM
 */

namespace Pyrobyte\ApiPayments\Action;


use Pyrobyte\ApiPayments\Engine\EngineAbstract;
use Pyrobyte\ApiPayments\Engine\Response;

abstract class ActionAbstract
{
    protected $engine = null;
    protected $client = null;

    public function setEngine(EngineAbstract $engine)
    {
        $this->engine = $engine;
    }

    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * Выполнение запроса через указанный движок парсера
     *
     * @param $request
     * @return Response
     */
    protected function request($request)
    {
        $response = $this->engine->request($request);

        return $response;
    }

    abstract function run();
}