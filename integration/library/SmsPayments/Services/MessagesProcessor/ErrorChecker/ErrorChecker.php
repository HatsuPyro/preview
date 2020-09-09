<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 11.12.2018
 * Time: 16:43
 */

namespace Pyrobyte\SmsPayments\Services\MessagesProcessor\ErrorChecker;

use Pyrobyte\SmsPayments\Exceptions\NotRegisteredException;
use Pyrobyte\SmsPayments\Exceptions\OrderNotConfirmedException;
use Pyrobyte\SmsPayments\Exceptions\PhoneNotEnoughBalanceException;
use Pyrobyte\SmsPayments\Exceptions\ServiceUnavailableException;
use Pyrobyte\SmsPayments\Exceptions\WrongNumberOrPasswordUnknownException;

class ErrorChecker extends ErrorCheckerAbstract
{
    protected function initPatterns()
    {
        // @todo перенести шаблоны только для киви в отловщик для киви
        $this->patterns = [
            [
                'patterns' => [
                    '.*?необходима.*?регистрация',
                ],
                'message' => 'Необходима регистрация',
                'exception' => NotRegisteredException::class,
            ],
            // Убиралось, а затем снова вернулось
            [
                'patterns' => [
                    '.*?вы.*?не.*?подтвердили.*?заказ',//Вы не подтвердили заказ услуги. Деньги не списаны.
                    '.*?вы.*?отказались.*?от.*?оплаты',//Вы отказались от оплаты услуги.
                ],
                'message' => 'Заказ не подтвержден',
                'exception' => OrderNotConfirmedException::class,
            ],
            [
                'patterns' => [
                    '.*?недостаточно.*?средств',//Извините, на вашем счете недостаточно средств.
                ],
                'message' => 'На телефоне недостаточно средств',
                'exception' => PhoneNotEnoughBalanceException::class,
            ],
            [
                'patterns' => [
                    'Неправильный номер телефона или пароль',////Неправильный номер телефона или пароль
                ],
                'exception' => WrongNumberOrPasswordUnknownException::class,
            ],
            [
                'patterns' => [
                    '.*сервис временно недоступен.*'//Сервис временно недоступен (БД)
                ],
                'message' => 'Сервис платежной системы временно недоступен',
                'exception' => ServiceUnavailableException::class
            ]
        ];
    }


}