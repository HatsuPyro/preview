<?php

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action;

use Pyrobyte\SmsPayments\Action\GetTransactionsAbstract;
use Pyrobyte\SmsPayments\Config;
use Pyrobyte\SmsPayments\Entities\Transaction;

/**
 * Класс для получения транзакций qiwi кошелька из смсок, получаемых с симки из симбокса
 * Class GetTransactions
 * @package Pyrobyte\SmsPayments\Payment\Qiwi\Action
 */
class GetTransactions extends GetTransactionsAbstract
{
    public $resultClass = \Pyrobyte\SmsPayments\Payment\Qiwi\Result\GetTransactions::class;


    protected function getTransactionPatterns()
    {
        return $this->transactionPatterns = [
            //+79586304017 otpravil vam 15.00 rub. Dlia polucheniia zaregistriruites qiwi.com
            //Postuplenie na summu 200.00 rub. Qiwi.com
            '(postuplenie na summu)' => [
                'type' => Transaction::TYPE_INCOME
            ],
            '(otpravil vam)' => [
                'type' => Transaction::TYPE_INCOME,
                'callback' => function (Transaction $transaction, $message) {
                    $matches = [];
                    if (preg_match('/(\d+).*?(otpravil)/imu', $message->get(), $matches)) {
                        $transaction->setPayer($matches[1]);
                    }
                    return $transaction;
                },
            ],

            '(отправил вам)' => [
                'type' => Transaction::TYPE_INCOME,
                'callback' => function (Transaction $transaction, $message) {
                    $matches = [];
                    if (preg_match('/(\d+).*?(отправил)/imu', $message->get(), $matches)) {
                        $transaction->setPayer($matches[1]);
                    }
                    return $transaction;
                },
            ],

            //Spisanie c +79006291409 na summu 9.00 rub.
            //Oplata po karte 4693****5201 SUM 13000.00 RUB (13310,00 RUR). BAL 89.00 RUR VB24
            '(spisanie c .*na summu)|(oplata po karte)' => [
                'type' => Transaction::TYPE_OUTCOME,
            ],
        ];
    }

    protected function getFromNumbers()
    {
        return [
            Config::getItem('qiwi_phone'),
            Config::getItem('qiwi_phone_additional')
        ];
    }

    protected function getTransactionBalance($message)
    {
        $balancePatterns = [
            '/.*?BAL.*?(\d*\.\d*).*/imu',
            '/.*?счет.*?(\d+(.\d+)*)/imu'
        ];

        foreach ($balancePatterns as $pattern) {
            if (preg_match($pattern, $message->get(), $matches)) {
                $resultBalance = floatval($matches[1]);
                return $resultBalance;
            }
        }
        return null;
    }

    protected function getSum($message, $matches)
    {
        $rublesPrefixes = ['rub', 'RUR', 'руб'];
        $existsRublesPrefixes = false;
        foreach ($rublesPrefixes as $rublesPrefix) {
            preg_match('/.*?(\d+\.\d+)\s*'. $rublesPrefix .'.*/imu', $message->get(), $sumMatches);
            if (!empty($sumMatches[1])) {
                $existsRublesPrefixes = true;
                break;
            }
        }
        if (!$existsRublesPrefixes) {
            throw new \Exception('В сообщении нету рублевого значения суммы');
        }
        return (float)$sumMatches[1];
    }
}