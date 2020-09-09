<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 11.07.19
 * Time: 14:04
 */

namespace Pyrobyte\SmsPayments\Payment\Beeline\Action;

use Pyrobyte\SmsPayments\Action\GetTransactionsAbstract;
use \Pyrobyte\SmsPayments\Payment\Beeline\Result\GetTransactions as Result;
use Pyrobyte\SmsPayments\Config;
use Pyrobyte\SmsPayments\Entities\Transaction;

class GetTransactions extends GetTransactionsAbstract
{
    protected $resultClass = Result::class;
    private $sumRegexp = '(\d+([\.,]\d+)*)';
    private $currencyFilterRegex = '\s*?руб';

    protected function getTransactionPatterns()
    {
        /*
         (Снятие, 7878) - Платёж 1336468223 на сумму 12.00 р. Код перевода 1007736090070. Комиссия 0.36 р. за оплату Альфа-Банк (online).
         /Переводы с карты на карту в мобильном приложении. Подробнее ruru.ru/appruru

         (Зачисление, Beeline@) - Платеж 20,00 руб. зачислен WebMoney. // Не платите за SMS!

         (Снятие, Beeline@) - Списано 39.99 руб. абонентской платы за 3 дн. Плата не была списана в срок из-за недостатка средств на балансе.
          По условиям вашего тарифа пакет услуг предоставляется в начале месяца в полном объеме и абонентская плата списывается за каждый день. Подробнее 05096 (беспл). Ваш Билайн.»
        */

        return $this->transactionPatterns = [
            'Платеж.*?' . $this->sumRegexp . $this->currencyFilterRegex => [
                'type' => Transaction::TYPE_INCOME
            ],

            'Списано.*?' . $this->sumRegexp . $this->currencyFilterRegex => [
                'type' => Transaction::TYPE_OUTCOME
            ],

            'на сумму.*?' . $this->sumRegexp => [
                'type' => Transaction::TYPE_OUTCOME,
                'callback' => function (Transaction $transaction, $message) {
                    $matches = [];
                    if (preg_match('/Комиссия.*?' . $this->sumRegexp . '/imu', $message->get(), $matches)) {
                        $transaction->setComission((float)str_replace(',', '.', $matches[1]));
                    }
                    return $transaction;
                },

            ],
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getFromNumbers()
    {
        return [
            Config::getItem('beeline_phone'),
            Config::getItem('service_ruru_phone'),
        ];
    }

    protected function getSum($message, $matches)
    {
        $commission = 0;
        $commissionMatches = [];
        if (preg_match('/на сумму.*?' . $this->sumRegexp . '/imu', $message->get())) {
            preg_match('/Комиссия.*?' . $this->sumRegexp . '/imu', $message->get(), $commissionMatches);
            $commission = $commissionMatches[1];
        }
        return (float)str_replace(',', '.', $matches[1]) + (float)str_replace(',', '.', $commission);
    }

}