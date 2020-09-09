<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 1/22/19
 * Time: 2:35 PM
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\ErrorChecker;


use Pyrobyte\SmsPayments\Exceptions\InsufficientFundsException;

class CheckPayChecker extends QiwiErrorChecker
{
    protected function initPatterns()
    {
        parent::initPatterns();

        $this->patterns = array_merge([
            [
                'patterns' => [
                    '.*?Недостаточно средств для оплаты на сумму',
                ],
                'message' => 'На кошельке недостаточно средств',
                'errorFunc' => function ($message, $error) {
                    $matches = [];
                    if (preg_match('/сумму.{0,10}?(\d+\.\d{2})/imu', $message->get(), $matches)) {
                        $balance = $matches[1];
                        $error->setBalance($balance);
                    }
                    return $error;
                },
                'exception' => InsufficientFundsException::class,
            ]
        ], $this->patterns);
    }
}