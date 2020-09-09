<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.12.2018
 * Time: 12:02
 */

namespace Pyrobyte\SmsPayments\Services\MessagesProcessor\ErrorChecker;


use Pyrobyte\SmsPayments\Exceptions\GotErrorMessageException;

abstract class ErrorCheckerAbstract
{
    protected $patterns = [];
    protected $engine = null;

    public function __construct($engine)
    {
        $this->initEngine($engine);
        $this->initPatterns();
    }

    abstract protected function initPatterns();

    protected function initEngine($engine)
    {
        $this->engine = $engine;
    }

    /**
     * Проверяет, не пришло ли сообщение ошибки
     * @param $message
     * @param $timeLimit
     * @throws GotErrorMessageException
     */
    public function check($message, $timeLimit)
    {
        $defaultException = GotErrorMessageException::class;
        $badPatterns = $this->patterns;

        foreach ($badPatterns as $pattern) {
            $pcrePatterns = $pattern['patterns'];
            foreach ($pcrePatterns as $pcrePattern) {
                if (preg_match('/' . $pcrePattern . '/imu', $message->get())) {

                    $errorMessage = $pattern['message'] ?? $pcrePattern;
                    if (!empty($pattern['writeMessage'])) {
                        $errorMessage .= ': ' . $message->get();
                    }

                    $exceptionClass = $pattern['exception'] ?? $defaultException;
                    $exception = new $exceptionClass($errorMessage);
                    if (!empty($pattern['errorFunc'])) {
                        $errorFunction = $pattern['errorFunc'];
                        $exception = $errorFunction($message, $exception, $timeLimit);
                    }

                    throw $exception;
                }
            }
        }
    }
}