<?php

namespace App\Extensions\Payment;

use App\Extensions\Payment\Adapter\Result\GetWallets;

interface PaymentInterface
{
    /**
     * Получает баланс для клиента
     * @param null $currency
     * @return mixed
     */
    public function getBalance($currency = null);

    /**
     * Получает историю операций за период времени
     * Формат дат: 2018-09-01T00:00:00
     * @param $fromDate - дата начала периода
     * @param $toDate - дата конца периода
     * @return mixed
     */
    public function getTransactions($fromDate, $toDate);

    /**
     * Массовое получение баланса
     * @return mixed
     */
    public function massGetBalance();

    /**
     * Массовое получение транзакций
     * @param $fromDate
     * @param $toDate
     * @return mixed
     */
    public function massGetTransactions($fromDate, $toDate);

    /**
     * Производит выплату на номер телефона
     * @param $phoneNumber
     * @param $sum
     * @return mixed
     */
    public function payPhone($phoneNumber, $sum);

    /**
     * Производит выплату на другой кошелек
     * @param $wallet
     * @param $sum
     * @return mixed
     */
    public function payWallet($wallet, $sum);

    /**
     * Производит выплату на карту
     * @param $card
     * @param $sum
     * @return mixed
     */
    public function payCard($card, $sum);

    /**
     * Производит выплату на банковский счет
     * @param $bankAccount
     * @param $sum
     * @return mixed
     */
    public function payBankAccount($bankAccount, $sum);

    /**
     * Получает подтверждение совершения платежа
     * @param $time
     * @param $sum
     * @return mixed
     */
    public function getPayConfirm($time, $sum);

    /**
     * Получает все кошельки
     * @return GetWallets
     */
    public function getWallets();
}