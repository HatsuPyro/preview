<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.10.2018
 * Time: 13:44
 */

namespace Pyrobyte\SmsPayments\Services\MessagesProcessor;

use Pyrobyte\SmsPayments\Exceptions\GetSmsTimeoutException;
use Pyrobyte\SmsPayments\Exceptions\SmsPaymentException;
use Pyrobyte\SmsPayments\Exceptions\UnknownException;
use Pyrobyte\SmsPayments\Logger;
use Pyrobyte\SmsPayments\Services\MessagesProcessor\ErrorChecker\ErrorChecker;

/**
 * Класс для обработки сообщений от платежной системы
 * Class MessagesProcessor
 * @package Pyrobyte\SmsPayments\Services\MessagesProcessor
 */
class MessagesProcessor
{
    protected $engine = null;
    /**
     * @var MessagesProcessorStateAbstract
     */
    private $state;
    /**
     * @var MessageProcessorResult
     */
    private $result = null;
    protected $steps;
    private $messages = [];
    private $lastStepMessages = [];
    private $messageChecker = null;
    /**
     * @var Timer
     */
    private $timer = null;
    private $timeLimit = null;
    private $parseFrom = null;
    private $provider = null;

    public function __construct($engine, array $steps, $timeLimit, $provider)
    {
        $this->timer = new Timer();
        $this->timeLimit = $timeLimit;
        $this->engine = $engine;
        $this->steps = $steps;
        $this->provider = $provider;
        $this->goNextStep();
        $this->messageChecker = new ErrorChecker($this->engine);
    }

    /**
     * Обрабатывает сообщения
     * @return mixed|null
     * @throws SmsPaymentException
     */
    public function processMessages()
    {
        try {

            $requestInterval = 1;
            do {
                $messages = $this->engine->getSms();
                $result = $this->parseMessages($messages);
                if ($result === null) {
                    sleep($requestInterval);
                }
                // Пока не удалось получить баланс и не истекло время на запросы
            } while ($result === null && ($this->timer->getSecondsPassed()) < $this->timeLimit);

            if ($result === null || $result->getResult() === null) {
                $lastStepMessages = $this->getLastStepMessages();
                if (!empty($lastStepMessages)) {
                    throw new UnknownException('Unknown response: ' . $this->implodeMessages($lastStepMessages));
                }
                throw new GetSmsTimeoutException('Time for sms operation is up');
            }

            return $result;
        } catch (SmsPaymentException $e) {
            $currentStateName = $this->state->getName();
            $e->setMessage('Ошибка обработки сообщений на шаге "' . $currentStateName . '": ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Парсит сообщения в поисках баланса
     * @param $messages
     * @return mixed|null
     */
    protected function parseMessages($messages)
    {
        foreach ($messages as $message) {
            $parseFrom = is_array($this->parseFrom) ? $this->parseFrom : [$this->parseFrom];
            // Нужно ли парсить это сообщение
            $needParse = false;
            if(in_array($message->getFrom(), $parseFrom)) {
                $needParse = true;
            }
            if(!$needParse) {
                continue;
            }

            $this->parse($message);

            if($this->isResultReady()) {
                return $this->getResult();
            }
        }

        return null;
    }

    /**
     * Задает, откуда(с какого номера) парсить сообщения
     * @param string|array $parseFrom
     */
    public function setParseFrom($parseFrom)
    {
        $this->parseFrom = $parseFrom;
    }

    /**
     * Переход на следующий шаг обработки сообщений
     * @return $this
     */
    protected function goNextStep()
    {
        $state = array_shift($this->steps);
        if(empty($state)) {
            return $this;
        }
        $this->lastStepMessages = [];
        $this->timer->refresh();
        if(is_a($state, MessageProcessorStateInterface::class, false)) {
            $this->state = $state;
        } else {
            $this->state = new $state();
        }
        $this->state->setEngine($this->engine);
        $this->state->setProvider($this->provider);
        $this->result = new MessageProcessorResult();
        return $this;
    }

    /**
     * Получает все полученные сообщения
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    private function addMessageIfNeeded($message)
    {
        foreach ($this->messages as $wrotenMessage) {
            if($wrotenMessage->getDate() == $message->getDate()
                && $wrotenMessage->getFrom() == $message->getFrom()
                && $wrotenMessage->get() == $message->get()) {
                return;
            }
        }
        $this->lastStepMessages[] = $message;
        $this->messages[] = $message;

        $logger = Logger::getInstance();
        $logger->addIncomeSms($message);
    }

    /**
     * Получает сообщения, полученные на последнем шаге
     * @return array
     */
    public function getLastStepMessages()
    {
        return $this->lastStepMessages;
    }

    /**
     * Обработать одно сообщение
     * @param $message
     * @return $this
     * @throws \Pyrobyte\SmsPayments\Exceptions\GotErrorMessageException
     */
    private function parse($message)
    {
        // Если сообщение было получено до начала текущего шага - оно нас не интересует
        if((float)$message->getDate() < $this->timer->getTime()) {
            return $this;
        }
        $this->addMessageIfNeeded($message);
        $this->messageChecker->check($message, $this->getActualTimeLimit());

        $result = $this->state->process($message);
        $this->result->setResult($result);
        $this->result->setTime($message->getDate());
        if($result !== null) {
            $this->goNextStep();
        }

        return $this;
    }

    /**
     * Получает актуальный остаток времени на задачу
     * @return int
     */
    private function getActualTimeLimit()
    {
        return $this->timeLimit - ($this->timer->getSecondsPassed());
    }

    /**
     * Готов ли результат выполнения всех действий
     * @return bool
     */
    public function isResultReady()
    {
        return empty($this->steps) && ($this->result->getResult() !== null);
    }

    /**
     * Получает итоговый результат выполнения
     * @return null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Устанавливает проверщика ошибочных сообщений
     * @param $messageChecker
     */
    public function setMessageChecker($messageChecker)
    {
        $this->messageChecker = $messageChecker;
    }

    /**
     * Объединяет текст всех сообщений в одну строку
     * @param $messages
     * @return string
     */
    private function implodeMessages($messages)
    {
        $messagesTexts = [];
        foreach ($messages as $message) {
            $messagesTexts[] = $message->get();
        }
        return implode(' | ', $messagesTexts);
    }

    /**
     * Устанавливает таймер
     * @param Timer $timer
     * @return $this
     */
    public function setTimer(Timer $timer)
    {
        $this->timer = $timer;
        return $this;
    }

}