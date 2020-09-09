<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 09.07.19
 * Time: 18:38
 */

namespace Pyrobyte\SmsPayments\Payment\Beeline\Action\MessagesProcessor\ErrorChecker;

use Pyrobyte\SmsPayments\Exceptions\InsufficientFundsException;
use Pyrobyte\SmsPayments\Exceptions\LockedException;
use Pyrobyte\SmsPayments\Exceptions\StartingBalanceNotUsed;
use Pyrobyte\SmsPayments\Services\MessagesProcessor\ErrorChecker\ErrorChecker;

class PayChecker extends ErrorChecker
{

    protected function initPatterns()
    {
        parent::initPatterns();
        $this->patterns = array_merge([
            [
                'patterns' => [
                    '.*Вы можете проводить оплату со счета мобильного телефона только с момента использования стартового баланса.*',
                ],
                'message' => 'Перевод не выполнен так как не потрачен стартовый баланс сим карты',
                'exception' => StartingBalanceNotUsed::class,
            ],
            [
                'patterns' => [
                    '.*Оплата не произведена. Уменьшите сумму запроса или пополните счет. После операции баланс вашего телефона не должен быть менее 50 рублей.*',
                ],
                'message' => 'Перевод не выполнен так как баланс кошелька меньше 50 рублей',
                'exception' => InsufficientFundsException::class,
            ],
            [
                'patterns' => [
                    '.*Оплата не произведена. Чтобы воспользоваться услугой, подтвердите персональные данные по Вашей SIM-карте, обратившись в любой офис Билайн.*',
                ],
                'message' => 'Перевод не выполнен, переводы с данной SIM-карты запрещены beeline',
                'exception' => LockedException::class,
            ],
        ], $this->patterns);
    }
}