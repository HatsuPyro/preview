<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.10.2018
 * Time: 15:22
 */

namespace Pyrobyte\SmsPayments\Services\MessagesProcessor;


abstract class MessagesProcessorStateAbstract implements MessageProcessorStateInterface
{
    protected $engine = null;
    protected $name = null;
    protected $provider = null;

    public function setEngine($engine)
    {
        $this->engine = $engine;
    }

    public function process($message)
    {
        $this->preProcess();
        return $this->doProcess($message);
    }

    protected function preProcess() {}

    /**
     * Должна возвращать результат выполнения, либо null если не был получен результат
     * @param $message
     * @return mixed
     */
    abstract public function doProcess($message);

    public function getName()
    {
        return $this->name;
    }

    /**
     * Задает провайдера симки
     * @param $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }
}