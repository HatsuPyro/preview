<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.11.2018
 * Time: 13:19
 */

namespace Pyrobyte\Logger;


/**
 * Class Logger
 * @package Pyrobyte\Logger
 */
class Logger
{
    protected $messages = [];
    private static $instances = [];

    private function __construct() { }

    /**
     * @return static
     */
    public static function getInstance()
    {
        $calledClass = get_called_class();

        if (!isset(self::$instances[$calledClass]))
        {
            self::$instances[$calledClass] = new $calledClass();
        }

        return self::$instances[$calledClass];
    }

    public static function clearInstances()
    {
        self::$instances = [];
    }

    public function addMessage($message, $level)
    {
        $this->messages[$level][] = $message;
        return $this;
    }

    public function setMessages(array $messages, $level)
    {
        $this->messages[$level] = $messages;
        return $this;
    }

    public function getMessages($level)
    {
        return $this->messages[$level] ?? [];
    }
}