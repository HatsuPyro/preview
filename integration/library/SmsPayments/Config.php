<?php

/**
 * Created by nikita@hotbrains.ru
 * Date: 19.10.2018
 * Time: 16:18
 */

namespace Pyrobyte\SmsPayments;

use Pyrobyte\Config\PyrobyteConfig;

class Config extends PyrobyteConfig
{
    protected static $config = [
        'qiwi_phone' => '7494',
        'qiwi_phone_additional' => 'QIWIWallet',
        'tele2_phone' => '159',
        'tele2_phone_additional' => 'Tele2',
        'sber_phone' => '900',
        'alfa_phone' => 'Alfa-Bank',
        'qiwi' => [
            'payout' => [
                'time' => 400,
            ],
            'balance' => [
                'time' => 150,
            ]
        ],
        'beeline' => [
            'payout' => [
                'time' => 400,
                'from_phones' => [
                    '8464',
                    '7878',
                ]
            ],
        ],
        'tele2' => [
            'payout' => [
                'time' => 500,
                // Телефоны, с которых парсить сообщения для операции перевода
                'from_phones' => [
                    '575',
                    '159',
                    '145',
                ]
            ],
        ],
        'ussd' => [
            'balance' => [
                'time' => 60,
            ]
        ],
        'sber' => [
            'transactions' => [
                // Максимальное время, за которое можно считать сообщения-транзакции одной
                'same_transactions_max_diff_time' => 600,
            ]
        ],
        'binbank_phone' => 'OTKRITIE',
        'open_phone' => 'OTKRITIE',
        'raiffeisen_phone' => 'Raiffeisen',
        'vtb_phone' => 'VTB',
        'tcs_phone' => 'Tinkoff@',
        'service_ruru_phone' => '7878',
        'beeline_phone' => 'Beeline@',
        'beeline_phone_pay_confirmation' => '8464',
        'mkb_phone' => 'MKB',
        'uralsib_phone' => 'URALSIB@',
        'gazprom_phone' => 'Telecard',
        'otp_phone' => 'OTP_Bank',
        'otp_phone_additional' => 'OTP Bank',
        'rs_phone' => 'RSB.RU',
        'psb_phone' => 'PSB',
        'ubrir_phone' => 'UBRR',
        'dom_phone' => 'Bank_DOM.RF',
        'rshb_phone' => 'RSHB',
        'fora_phone' => 'FORA-BANK',
        'atb_phone' => 'ATB',
    ];

    protected static $defferedConfig = [];
}
