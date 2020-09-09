<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 20.09.2018
 * Time: 16:02
 */

namespace App\Extensions\Payment;

use App\Extensions\Payment\Exception\MethodNotImplementedException;

abstract class PaymentAbstract implements PaymentInterface
{
    const EXPECTED_DATE_FORMAT = 'Y-m-d\TH:i:s';

    protected $tmpPath;
    protected $proxy;

    /**
     * Токен антикапчи
     * @var
     */
    public static $anticaptcha;

    public function __construct($params, $tmpPath = null)
    {
        $this->tmpPath = $tmpPath;
        $this->proxy = $params['proxy'] ?? null;
    }

    /**
     * Получает баланс для клиента
     * @param null $currency
     * @return mixed
     */
    public function getBalance($currency = null)
    {
        throw new MethodNotImplementedException('Method getBalance is not implemented in current adapter');
    }

    /**
     * @inheritdoc
     */
    public function getPayConfirm($time, $sum)
    {
        throw new MethodNotImplementedException('Method getPayConfirm is not implemented in current adapter');
    }

    protected function doGetTransactions($fromDate, $toDate) {
        throw new MethodNotImplementedException('Method doGetTransactions is not implemented in current adapter');
    }


    /**
     * Получает историю операций за период времени
     * Формат дат: 2018-09-01T00:00:00
     * @param $fromDate - дата начала периода
     * @param $toDate - дата конца периода
     * @return mixed
     * @throws \Exception
     */
    public function getTransactions($fromDate, $toDate)
    {
        $dateFormat = self::EXPECTED_DATE_FORMAT;

        $rules = ['fromDate' => 'required|date_format:' . $dateFormat, 'toDate' => 'required|date_format:' . $dateFormat];
        $validationData = ['fromDate' => $fromDate, 'toDate' => $toDate];

        $validator = Validator::make($validationData, $rules);

        if($validator->fails()) {
            throw new \Exception(implode($validator->getMessages()));
        }

        return $this->doGetTransactions($fromDate, $toDate);
    }

    /**
     * @inheritdoc
     */
    public function payPhone($phoneNumber, $sum)
    {
        throw new MethodNotImplementedException('Method payPhone is not implemented in current adapter');
    }

    /**
     * @inheritdoc
     */
    public function payWallet($wallet, $sum)
    {
        throw new MethodNotImplementedException('Method payWallet is not implemented in current adapter');
    }

    /**
     * @inheritdoc
     */
    public function payCard($card, $sum)
    {
        throw new MethodNotImplementedException('Method payCard is not implemented in current adapter');
    }

    /**
     * @inheritdoc
     */
    public function payBankAccount($bankAccount, $sum)
    {
        throw new MethodNotImplementedException('Method payBankAccount is not implemented in current adapter');
    }

    /**
     * @inheritdoc
     */
    public function getWallets()
    {
        throw new MethodNotImplementedException('Method getWallets in not implemented in current adapter');
    }

    public static function setAnticaptchaToken($token)
    {
        self::$anticaptcha = $token;
    }

    /**
     * @inheritdoc
     */
    public function massGetBalance()
    {
        throw new MethodNotImplementedException('Method massGetBalance in not implemented in current adapter');
    }

    /**
     * @inheritdoc
     */
    public function massGetTransactions($fromDate, $toDate)
    {
        throw new MethodNotImplementedException('Method massGetTransactions in not implemented in current adapter');
    }
}