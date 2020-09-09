<?php
/**
 * Created by https://github.com/Wheiss
 * Date: 11.11.2018
 * Time: 0:07
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\ErrorChecker;


use Pyrobyte\SmsPayments\Exceptions\NotRegisteredException;
use Pyrobyte\SmsPayments\Exceptions\PaymentDeniedException;
use Pyrobyte\SmsPayments\Exceptions\InsufficientFundsException;
use Pyrobyte\SmsPayments\Exceptions\WrongFormatCommandException;
use Pyrobyte\SmsPayments\Logger;
use Pyrobyte\SmsPayments\Services\MessagesProcessor\ErrorChecker\ErrorChecker;

class PayChecker extends QiwiErrorChecker
{
    protected function initPatterns()
    {
        parent::initPatterns();
        $this->patterns = array_merge([
            [
                'patterns' => [
                    '.*?недостаточно.*?средств.*?qiwi',//Недостаточно средств на счете QIWI Кошельке 0.00 руб.. Необходимо 5600.00 руб.
                ],
                'message' => 'На кошельке недостаточно средств',
                'errorFunc' => function ($message, $error) {
                    $matches = [];
                    if (preg_match('/кошел.{0,10}?(\d+\.\d{2})/imu', $message->get(), $matches)) {
                        $balance = $matches[1];
                        $error->setBalance($balance);
                    }
                    return $error;
                },
                'exception' => InsufficientFundsException::class,
            ],
            [
                'patterns' => [
                    'Неправильный номер телефона или пароль', //Неправильный номер телефона или пароль
                ],
                'errorFunc' => function ($message, $error) {
                    $errorMessage = 'Кошелек не зарегистрирован. Текст сообщения: ' . $message;
                    $error->setMessage($errorMessage);
                    return $error;
                },
                'exception' => NotRegisteredException::class,
            ],
        ], $this->patterns, [
            [
                'patterns' => [
                    '.*?платеж.*?отклонен',//Платеж отклонен. Подробнее на https //qiwi.com/report/list.action
                    ],
                'message' => 'Платеж отклонен',
                'exception' => PaymentDeniedException::class,
                'errorFunc' => function ($message, $error, $timeLimit) {
                    // Этот callback разбирает второе сообщение об ошибке, разъясняющее причину отклонения

                    $date = $message->getDate();

                    $targetNumber = 'QIWIWallet';

                    $requestInterval = 1;
                    do {
                        $messages = $this->engine->getSms();

                        foreach ($messages as $newMessage) {
                            if($newMessage->getDate() < $date || $newMessage->getFrom() !== $targetNumber) {
                                continue;
                            }
                            $logger = Logger::getInstance();
                            $logger->addIncomeSms($message);

                            $errorClass = get_class($error);

                            return new $errorClass($error->getMessage() . '. Причина: ' . $newMessage->get());
                        }
                        sleep($requestInterval);

                        // Пока не удалось получить причину и не истекло время на запросы
                    } while((time() - $date) < $timeLimit);

                    return $error;
                },
            ],
            [
                'patterns' => [
                    '.*?Неверный формат команды.*?',
                    '.*QIWI Формат номер сумма, где номер в формате.*' //QIWI Формат номер сумма, где номер в формате 79123456789.
                ],
                'message' => 'Неверный формат команды',
                'exception' => WrongFormatCommandException::class,
            ],
            [
                'patterns' => [
                    '.*?пользователь.*?не зарегестрирован',//Пользователь 2200800202628415 не зарегестрирован, регистрация https //qiwi.com
                ],
                'message' => 'Платеж отклонен так как кошелек назначения перевода не зарегистрирован в платежной системе',
                'exception' => PaymentDeniedException::class,
            ],
        ]);
    }
}