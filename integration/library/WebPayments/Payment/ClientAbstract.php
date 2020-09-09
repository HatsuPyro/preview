<?php

/**
 * Основной класс-клиент для работы с платежной системой
 */

namespace Pyrobyte\WebPayments\Payment;

abstract class ClientAbstract
{
    const ENGINE_GUZZLE = 'guzzle';
    const ENGINE_CURL = 'curl';

    public static $defaultEngine = self::ENGINE_GUZZLE;

    protected $engine;
    protected $tmpPath;
    protected $cookies;
    protected $proxy = null;
    protected $doAuthorization = true;

    /**
     * Задает прокси, через который будут проходить запросы
     * @param $proxy
     * @return $this
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
        return $this;
    }

    /**
     * Инициализация клиента
     * @param $tmpPath
     */
    public function init($tmpPath)
    {
        $this->setTmpPath($tmpPath);
        $this->cookies = $this->getCookiesFileName();
        $this->initEngine();
    }

    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Формирует название файла куки. В каждом клиенте должен был реализован по своему
     * @return string
     */
    public function getCookiesFileName()
    {
        return 'cookies.txt';
    }

    /**
     * Инициализация движка парсера
     */
    public function initEngine()
    {
        switch (self::$defaultEngine) {
            case self::ENGINE_GUZZLE:
            default:
                $this->engine = new \Pyrobyte\WebPayments\Engine\Guzzle($this->getCookiesFilePath(), $this->proxy);
        }
    }

    /**
     * Устанавливает временную директорию под куки и создает папку, если ее нет
     * @param $path
     */
    public function setTmpPath($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $this->tmpPath = $path;
    }


    public function setDoAuthorization($doAuthorization) {
        $this->doAuthorization = $doAuthorization;
    }

    public function getDoAuthorization() {
        return $this->doAuthorization;
    }

    /**
     * @return null|string
     */
    public function getCookiesFilePath()
    {
        return $this->tmpPath ? $this->tmpPath . DIRECTORY_SEPARATOR . $this->cookies : null;
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