<?php


namespace App\Extensions\Payment;

use App\Extensions\Payment\Adapter\Alfa\AlfaSms;
use App\Extensions\Payment\Adapter\Beeline\BeelineSms;
use App\Extensions\Payment\Adapter\Beeline\BeelineUssd;
use App\Extensions\Payment\Adapter\DomRF\DomRFWeb;
use App\Extensions\Payment\Adapter\Gazprom\GazpromSms;
use App\Extensions\Payment\Adapter\Mkb\MkbSms;
use App\Extensions\Payment\Adapter\Mkb\MkbWeb;
use App\Extensions\Payment\Adapter\Otp\OtpSms;
use App\Extensions\Payment\Adapter\Otp\OtpWeb;
use App\Extensions\Payment\Adapter\SvyazBank\SvyazBankWeb;
use App\Extensions\Payment\Adapter\Pochta\PochtaWeb;
use App\Extensions\Payment\Adapter\Psb\PsbSms;
use App\Extensions\Payment\Adapter\Qiwi\QiwiAdgroupApi;
use App\Extensions\Payment\Adapter\Qiwi\OfficialApi;
use App\Extensions\Payment\Adapter\Qiwi\QiwiAdgroupCardApi;
use App\Extensions\Payment\Adapter\Qiwi\QiwiP2PApi;
use App\Extensions\Payment\Adapter\Qiwi\QiwiSms;
use App\Extensions\Payment\Adapter\Qiwi\QiwiWeb;
use App\Extensions\Payment\Adapter\Rs\RsSms;
use App\Extensions\Payment\Adapter\Sber\SberSms;
use App\Extensions\Payment\Adapter\Open\OpenSms;
use App\Extensions\Payment\Adapter\Tcs\TcsSms;
use App\Extensions\Payment\Adapter\Raiffeisen\RaiffeisenSms;
use App\Extensions\Payment\Adapter\Ubrir\UbrirSms;
use App\Extensions\Payment\Adapter\Uralsib\UralsibSms;
use App\Extensions\Payment\Adapter\Vtb\VtbSms;
use App\Extensions\Payment\Adapter\Vtb\VtbWeb;
use App\Extensions\Payment\Adapter\Yandex\YandexAdgroupApi;
use App\Extensions\Payment\Adapter\Yandex\YandexAdgroupCardApi;
use App\Extensions\Payment\Exception\RequiredParamIsMissingException;
use Illuminate\Support\Facades\Log;

/**
 * Обертка для работы с различными платежками и каналами (api, web, ...),
 * предоставляет единый доступ к функциям: получение баланса, отправка средств, получение истории операций
 * Class Manager
 * @package App\Extensions\Payment
 */
class Manager
{
    /**
     * Типы платежных систем
     */
    const PAYMENT_QIWI_CARD = 'qiwi_card';
    const PAYMENT_QIWI = 'qiwi';
    const PAYMENT_TELE2 = 'tele2';
    const PAYMENT_BINBANK = 'binbank';
    const PAYMENT_MEGAFON = 'megafon';
    const PAYMENT_ALFA = 'alfa';
    const PAYMENT_SBER = 'sber';
    const PAYMENT_OPEN = 'open';
    const PAYMENT_VTB = 'vtb';
    const PAYMENT_RAIFFEISEN = 'raiffeisen';
    const PAYMENT_TCS = 'tcs';
    const PAYMENT_BEELINE = 'beeline';
    const PAYMENT_URALSIB = 'uralsib';
    const PAYMENT_MKB = 'mkb';
    const PAYMENT_GAZPROM = 'gazprom';
    const PAYMENT_RS = 'rs';
    const PAYMENT_PSB = 'psb';
    const PAYMENT_OTP = 'otp';
    const PAYMENT_UBRIR = 'ubrir';
    const PAYMENT_DOMRF = 'home';
    const PAYMENT_YANDEX_CARD = 'yandex_card';
    const PAYMENT_YANDEX = 'yandex';
    const PAYMENT_POCHTA = 'pochta';
    const PAYMENT_RSHB = 'rshb';
    const PAYMENT_SVYAZ = 'svyaz';
    const PAYMENT_FORA = 'fora';
    const PAYMENT_ATB = 'atb';

    /**
     * Методы
     */
    const METHOD_GET_BALANCE = 'getBalance';
    const METHOD_GET_TRANSACTIONS = 'getTransactions';
    const METHOD_MASS_GET_BALANCE = 'massGetBalance';
    const METHOD_MASS_GET_TRANSACTIONS = 'massGetTransactions';
    const METHOD_PAY_PHONE = 'payPhone';
    const METHOD_PAY_SELF_PHONE = 'paySelfPhone';
    const METHOD_PAY_WALLET = 'payWallet';
    const METHOD_PAY_ALFA = 'payAlfa';
    const METHOD_PAY_CARD = 'payCard';
    const METHOD_PAY_BANK_ACCOUNT = 'payBankAccount';
    const METHOD_GET_PAY_CONFIRM = 'getPayConfirm';
    const METHOD_UNSUBSCRIBE = 'unsubscribe';
    const METHOD_CHECK_PAYOUT = 'checkPayout';
    const METHOD_UPDATE_BALANCE = 'update_balance';


    /**
     * Каналы работы с сервисами платежек
     */
    const CHANNEL_WEB = 'web';
    const CHANNEL_API = 'api';
    const CHANNEL_SMS = 'sms';
    const CHANNEL_USSD = 'ussd';
    const CHANNEL_API_ADGROUP = 'api_adgroup';
    const CHANNEL_API_P2P = 'api_p2p';

    const CHANNELS = [
        self::CHANNEL_WEB,
        self::CHANNEL_API,
        self::CHANNEL_SMS,
        self::CHANNEL_USSD,
        self::CHANNEL_API_ADGROUP,
        self::CHANNEL_API_P2P,
    ];

    /**
     * Конфиг ларавеля с платежками
     * @var
     */
    public static $config;

    /**
     * Временная папка для кук и т.д.
     * @var
     */
    public static $tmpPath;

    /**
     * Возвращает адаптер нужной платежки
     * TODO: вынести конкретные адаптеры в конфиг или через контейнер разруливать (но тогда появится зависимость от этого контейнера)
     * @param $payment
     * @param array $params
     * @return PaymentInterface
     * @throws \App\Extensions\Payment\Exception\AuthenticationException
     */
    public static function getPayment($payment, array $params)
    {
        try {

            $tmpPath = self::$tmpPath . DIRECTORY_SEPARATOR . $payment;
            if(empty($params['channel'])) {
                throw new RequiredParamIsMissingException('Required param channel is missing');
            }
            $paymentClass = self::getPaymentClass($payment, $params['channel']);
            return new $paymentClass($params, $tmpPath);

        } catch (\App\Extensions\Payment\Exception\AuthenticationException $e) {
            Log::channel('tele2_auth_log')->error($e);
            throw $e;
        }
    }

    public static function checkPaymentMethod($payment, $channel, $method)
    {
        $checker = new MethodChecker();
        try {
            $paymentClass = self::getPaymentClass($payment, $channel);
        } catch (\Exception $e) {
            return false;
        }

        return $checker->checkMethod($paymentClass, $method);
    }

    public static function getAuthSmsClass($payment) {
        $authSmsClass = [
            self::PAYMENT_ALFA => \Pyrobyte\SmsPayments\Payment\Alfa\Action\GetAuthSms::class,
            self::PAYMENT_GAZPROM => \Pyrobyte\SmsPayments\Payment\Gazprom\Action\GetAuthSms::class,
            self::PAYMENT_DOMRF => \Pyrobyte\SmsPayments\Payment\Dom\Action\GetAuthSms::class,
            self::PAYMENT_RSHB => \Pyrobyte\SmsPayments\Payment\Rshb\Action\GetAuthSms::class,
            self::PAYMENT_UBRIR => \Pyrobyte\SmsPayments\Payment\Ubrir\Action\GetAuthSms::class,
            self::PAYMENT_PSB => \Pyrobyte\SmsPayments\Payment\Psb\Action\GetAuthSms::class,
            self::PAYMENT_VTB => \Pyrobyte\SmsPayments\Payment\Vtb\Action\GetAuthSms::class,
            self::PAYMENT_TCS => \Pyrobyte\SmsPayments\Payment\Tcs\Action\GetAuthSms::class,
        ];

        if (empty($authSmsClass[$payment])) {
            $authSmsClass[$payment] = false;
        }

        return $authSmsClass[$payment];
    }

    private static function getPaymentClass($payment, $channel)
    {
        $paymentsTable = [
            self::PAYMENT_QIWI_CARD => [
                self::CHANNEL_API_ADGROUP => QiwiAdgroupCardApi::class,
            ],
            self::PAYMENT_QIWI => [
                self::CHANNEL_WEB => QiwiWeb::class,
                self::CHANNEL_API => OfficialApi::class,
                self::CHANNEL_SMS => QiwiSms::class,
                self::CHANNEL_API_ADGROUP => QiwiAdgroupApi::class,
                self::CHANNEL_API_P2P => QiwiP2PApi::class,
            ],
            self::PAYMENT_TELE2 => [
                self::CHANNEL_WEB => \App\Extensions\Payment\Adapter\Tele2\Tele2Web::class,
                self::CHANNEL_USSD => \App\Extensions\Payment\Adapter\Tele2\Tele2Ussd::class,
                self::CHANNEL_SMS => \App\Extensions\Payment\Adapter\Tele2\Tele2Sms::class,
            ],
            self::PAYMENT_BINBANK => [
                self::CHANNEL_WEB => \App\Extensions\Payment\Adapter\Binbank\BinbankWeb::class,
                self::CHANNEL_SMS => \App\Extensions\Payment\Adapter\Binbank\BinbankSms::class,
            ],
            self::PAYMENT_MEGAFON => [
                self::CHANNEL_WEB => \App\Extensions\Payment\Adapter\Megafon\MegafonWeb::class,
                self::CHANNEL_SMS => \App\Extensions\Payment\Adapter\Megafon\MegafonSms::class,
                self::CHANNEL_USSD => \App\Extensions\Payment\Adapter\Megafon\MegafonUssd::class,
            ],
            self::PAYMENT_SBER => [
                self::CHANNEL_SMS => SberSms::class,
            ],
            self::PAYMENT_ALFA => [
                self::CHANNEL_SMS => AlfaSms::class,
            ],
            self::PAYMENT_OPEN => [
                self::CHANNEL_SMS => OpenSms::class,
            ],
            self::PAYMENT_VTB => [
                self::CHANNEL_WEB => VtbWeb::class,
                self::CHANNEL_SMS => VtbSms::class,
            ],
            self::PAYMENT_RAIFFEISEN => [
                self::CHANNEL_SMS => RaiffeisenSms::class,
            ],
            self::PAYMENT_TCS => [
                self::CHANNEL_SMS => TcsSms::class,
            ],
            self::PAYMENT_BEELINE => [
                self::CHANNEL_SMS => BeelineSms::class,
                self::CHANNEL_USSD => BeelineUssd::class,
            ],
            self::PAYMENT_URALSIB => [
                self::CHANNEL_SMS => UralsibSms::class,
            ],
            self::PAYMENT_MKB => [
                self::CHANNEL_SMS => MkbSms::class,
                self::CHANNEL_WEB => MkbWeb::class,
            ],
            self::PAYMENT_GAZPROM => [
                self::CHANNEL_SMS => GazpromSms::class,
            ],
            self::PAYMENT_RS => [
                self::CHANNEL_SMS => RsSms::class,
            ],
            self::PAYMENT_OTP => [
                self::CHANNEL_WEB => OtpWeb::class,
                self::CHANNEL_SMS => OtpSms::class,
            ],
            self::PAYMENT_PSB => [
                self::CHANNEL_SMS => PsbSms::class,
            ],
            self::PAYMENT_UBRIR => [
                self::CHANNEL_SMS => UbrirSms::class,
            ],
            self::PAYMENT_DOMRF => [
                self::CHANNEL_WEB => DomRFWeb::class,
            ],
            self::PAYMENT_YANDEX_CARD => [
                self::CHANNEL_API_ADGROUP => YandexAdgroupCardApi::class,
            ],
            self::PAYMENT_YANDEX => [
                self::CHANNEL_API_ADGROUP => YandexAdgroupApi::class,
            ],
            self::PAYMENT_POCHTA => [
                self::CHANNEL_WEB => PochtaWeb::class,
            ],
            self::PAYMENT_SVYAZ => [
                self::CHANNEL_WEB => SvyazBankWeb::class,
            ],
            self::PAYMENT_RSHB => [
                self::CHANNEL_SMS => \App\Extensions\Payment\Adapter\Rshb\RshbSms::class,
            ],
            self::PAYMENT_FORA => [
                self::CHANNEL_SMS => \App\Extensions\Payment\Adapter\Fora\ForaSms::class,
                self::CHANNEL_WEB => \App\Extensions\Payment\Adapter\Fora\ForaWeb::class,
            ],
            self::PAYMENT_ATB => [
                self::CHANNEL_SMS => \App\Extensions\Payment\Adapter\Atb\AtbSms::class,
            ],
        ];

        if (empty($paymentsTable[$payment])) {
            throw new \Exception('Undefined payment type: ' . $payment);
        }

        if (empty($paymentsTable[$payment][$channel])) {
            throw new \Exception('Undefined channel type ' . $channel . ' for payment ' . $payment);
        }

        return $paymentsTable[$payment][$channel];
    }

    public static function setConfig($config)
    {
        return self::$config = $config;
    }

    public static function setTmpPath($path)
    {
        return self::$tmpPath = $path;
    }

    /**
     * Возвращает все платежные системы
     * @return array
     */
    public static function getAllPaymentSystem() {
        return [
            self::PAYMENT_QIWI_CARD,
            self::PAYMENT_QIWI,
            self::PAYMENT_TELE2,
            self::PAYMENT_BINBANK,
            self::PAYMENT_MEGAFON,
            self::PAYMENT_ALFA,
            self::PAYMENT_SBER,
            self::PAYMENT_OPEN,
            self::PAYMENT_VTB,
            self::PAYMENT_RAIFFEISEN,
            self::PAYMENT_TCS,
            self::PAYMENT_BEELINE,
            self::PAYMENT_URALSIB,
            self::PAYMENT_MKB,
            self::PAYMENT_GAZPROM,
            self::PAYMENT_RS,
            self::PAYMENT_PSB,
            self::PAYMENT_OTP,
            self::PAYMENT_UBRIR,
            self::PAYMENT_DOMRF,
            self::PAYMENT_YANDEX_CARD,
            self::PAYMENT_YANDEX,
            self::PAYMENT_POCHTA,
            self::PAYMENT_SVYAZ,
        ];
    }

    /**
     * Возвращает электронные кошельки
     */
    public static function getEWallets() {
        return [
            self::PAYMENT_QIWI,
            self::PAYMENT_YANDEX,
        ];
    }

    /**
     * Возвращает мобильные ПС.
     */
    public static function getMobilePaymentSystems() {
        return [
            self::PAYMENT_TELE2,
            self::PAYMENT_MEGAFON,
            self::PAYMENT_BEELINE,
        ];
    }

    /**
     * Возвращает банки.
     */
    public static function getBanks() {
        return [
            self::PAYMENT_QIWI_CARD,
            self::PAYMENT_BINBANK,
            self::PAYMENT_ALFA,
            self::PAYMENT_SBER,
            self::PAYMENT_OPEN,
            self::PAYMENT_VTB,
            self::PAYMENT_RAIFFEISEN,
            self::PAYMENT_TCS,
            self::PAYMENT_URALSIB,
            self::PAYMENT_MKB,
            self::PAYMENT_GAZPROM,
            self::PAYMENT_RS,
            self::PAYMENT_PSB,
            self::PAYMENT_OTP,
            self::PAYMENT_UBRIR,
            self::PAYMENT_DOMRF,
            self::PAYMENT_YANDEX_CARD,
            self::PAYMENT_POCHTA,
            self::PAYMENT_SVYAZ,
        ];
    }
}