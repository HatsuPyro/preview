<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 23.10.2018
 * Time: 11:17
 */

namespace App\Extensions\Payment;


/**
 * Проверяет может ли библиотека выполнить соответствующий метод
 * Class MethodChecker
 * @package App\Extensions\Payment
 */
class MethodChecker
{
    public function checkMethod($class, $method)
    {
        // Что умеют адаптеры платежек
        $methodAvailabilityMap = [
            \App\Extensions\Payment\Adapter\Tele2\Tele2Web::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_GET_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Tele2\Tele2Ussd::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_UNSUBSCRIBE => true,
                Manager::METHOD_CHECK_PAYOUT => true,
            ],
            \App\Extensions\Payment\Adapter\Tele2\Tele2Sms::class => [
                Manager::METHOD_PAY_CARD => true,
                Manager::METHOD_GET_PAY_CONFIRM => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_CHECK_PAYOUT => true,
            ],
            \App\Extensions\Payment\Adapter\Binbank\BinbankWeb::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_GET_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Qiwi\QiwiSms::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_PAY_PHONE => true,
                Manager::METHOD_PAY_SELF_PHONE => true,
                Manager::METHOD_PAY_CARD => true,
                Manager::METHOD_PAY_WALLET => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_GET_PAY_CONFIRM => true,
            ],
            \App\Extensions\Payment\Adapter\Megafon\MegafonUssd::class => [
                Manager::METHOD_GET_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Qiwi\QiwiAdgroupApi::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_MASS_GET_BALANCE => true,
                Manager::METHOD_MASS_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Qiwi\QiwiAdgroupCardApi::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_MASS_GET_BALANCE => true,
                Manager::METHOD_MASS_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Qiwi\QiwiP2PApi::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Binbank\BinbankSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
            ] ,
            \App\Extensions\Payment\Adapter\Alfa\AlfaSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_UPDATE_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Sber\SberSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_UPDATE_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Open\OpenSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_UPDATE_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Tcs\TcsSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_UPDATE_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Raiffeisen\RaiffeisenSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_UPDATE_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Beeline\BeelineSms::class => [
                Manager::METHOD_PAY_BANK_ACCOUNT => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Mkb\MkbSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Mkb\MkbWeb::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Uralsib\UralsibSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_UPDATE_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Beeline\BeelineUssd::class => [
                Manager::METHOD_GET_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Psb\PsbSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_UPDATE_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Otp\OtpWeb::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Otp\OtpSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Gazprom\GazpromSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_UPDATE_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Rs\RsSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_UPDATE_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\Ubrir\UbrirSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_UPDATE_BALANCE => true,
            ],
            \App\Extensions\Payment\Adapter\DomRF\DomRFWeb::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Vtb\VtbWeb::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Vtb\VtbSms::class => [
                  Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Yandex\YandexAdgroupApi::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_MASS_GET_BALANCE => true,
                Manager::METHOD_MASS_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Yandex\YandexAdgroupCardApi::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
                Manager::METHOD_MASS_GET_BALANCE => true,
                Manager::METHOD_MASS_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Pochta\PochtaWeb::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\SvyazBank\SvyazBankWeb::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],

            \App\Extensions\Payment\Adapter\Megafon\MegafonWeb::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Rshb\RshbSms::class => [
                Manager::METHOD_UPDATE_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Fora\ForaSms::class => [
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Fora\ForaWeb::class => [
                Manager::METHOD_GET_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],
            \App\Extensions\Payment\Adapter\Atb\AtbSms::class => [
                Manager::METHOD_UPDATE_BALANCE => true,
                Manager::METHOD_GET_TRANSACTIONS => true,
            ],

            ];

        return $methodAvailabilityMap[$class][$method] ?? false;
    }
}