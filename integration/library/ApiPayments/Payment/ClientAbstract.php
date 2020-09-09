<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/15/19
 * Time: 4:22 PM
 */

namespace Pyrobyte\ApiPayments\Payment;


use Pyrobyte\ApiPayments\Action\ActionAbstract;
use Pyrobyte\ApiPayments\Engine\Guzzle;

/**
 * Class ClientAbstract
 * @package Pyrobyte\ApiPayments\Payment
 */
class ClientAbstract
{
    const ENGINE_GUZZLE = 'guzzle';
    const ENGINE_CURL = 'curl';

    public static $defaultEngine = self::ENGINE_GUZZLE;

    protected $engine;

    public function __construct()
    {
        $this->initEngine();
    }

    /**
     * Инициализация клиента
     * @param $tmpPath
     */
    public function init()
    {
        $this->initEngine();
    }

    /**
     * Инициализация движка парсера
     */
    public function initEngine()
    {
        switch (self::$defaultEngine) {
            case self::ENGINE_GUZZLE:
            default:
                $this->engine = new Guzzle();
        }
    }

    /**
     * Вызывает действия над кошельком
     *
     * @param ActionAbstract $action
     * @return mixed
     */
    public function call(ActionAbstract $action)
    {
        $action->setEngine($this->engine);
        $action->setClient($this);

        return $action->run();
    }
}