<?php

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\ErrorChecker;


use Pyrobyte\SmsPayments\Exceptions\LockedException;
use Pyrobyte\SmsPayments\Exceptions\NotRegisteredException;
use Pyrobyte\SmsPayments\Exceptions\PaymentDeniedException;
use Pyrobyte\SmsPayments\Exceptions\WrongDefineMobileOperatorException;
use Pyrobyte\SmsPayments\Services\MessagesProcessor\ErrorChecker\ErrorChecker;

/**
 * Чекер для отлова ошибок от киви
 * Class QiwiErrorChecker
 * @package Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\ErrorChecker
 */
class QiwiErrorChecker extends ErrorChecker
{
    protected function initPatterns()
    {
        parent::initPatterns();
        $this->patterns = array_merge([
            [
                'patterns' => [
                    'Неправильный номер телефона или пароль',////Неправильный номер телефона или пароль
                ],
                'errorFunc' => function ($message, $error) {
                    $errorMessage = 'Кошелек не зарегистрирован. Текст сообщения: ' . $message->get();
                    $error->setMessage($errorMessage);
                    return $error;
                },
                'exception' => NotRegisteredException::class,
            ],
            [
                'patterns' => [
                    'Ваш кошелек заблокирован'//Ваш кошелек заблокирован.&lt
                ],
                'errorFunc' => function ($message, $error) {
                    $errorMessage = 'Кошелек заблокирован платежной системой. Текст сообщения: ' . $message->get();
                    $error->setMessage($errorMessage);
                    return $error;
                },
                'exception' => LockedException::class
            ],
            [
                'patterns' => [
                    '.*Не удалось определить оператора сотовой связи.*' //QIWI Не удалось определить оператора сотовой связи для номера 79313975872
                ],
                'errorFunc' => function ($message, $error) {
                    $errorMessage = 'Платежная система не смогла определить оператора сотовой связи у номера. Текст сообщения: ' . $message->get();
                    $error->setMessage($errorMessage);
                    return $error;
                },
                'exception' => WrongDefineMobileOperatorException::class
            ],
            [
                'patterns' => [
                    '.*Доступ к коротким номерам запрещен.*' //Доступ к коротким номерам запрещен. Отключить запрет платных коротких номеров *526*0#
                ],
                'message' => 'Операция не выполнена так как доступ к коротким номерам запрещен на симкарте',
                'exception' => PaymentDeniedException::class
            ]
        ], $this->patterns);
    }
}