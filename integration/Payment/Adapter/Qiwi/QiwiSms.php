<?php

/**
 * Адаптер платежной системы QIWI для работы с библиотекой Pyrobyte\QiwiSms (не написана еще)
 */

namespace App\Extensions\Payment\Adapter\Qiwi;

use App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions\GetBalance;
use App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions\GetPayConfirm;
use App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions\GetTransactions;
use App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions\SmsActionAbstract;
use App\Extensions\Payment\Adapter\QiwiWeb;
use App\Extensions\Payment\Adapter\ErrorCatcher\SmsErrorCatcher;
use Pyrobyte\SmsPayments\Payment\Qiwi\Client;
use \App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions\PayPhone as QiwiPayPhone;
use \App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions\PayWallet as QiwiPayWallet;
use \App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions\PaySelfPhone as QiwiPaySelfPhone;
use \App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions\PayCard as QiwiPayCard;

Class QiwiSms extends QiwiWeb implements \App\Extensions\Payment\PaymentInterface
{
    private $client = null;

    public function __construct($params, $tmpPath = null)
    {
        parent::__construct($params, $tmpPath);

        $this->client = new Client($params);
    }

    private function doAction(SmsActionAbstract $action)
    {
        $action->setClient($this->client);

        return SmsErrorCatcher::catch(function() use ($action) {
            return $action->do();
        });
    }

    /**
     * @inheritdoc
     */
    public function getBalance($currency = null)
    {
        return $this->doAction(new GetBalance());
    }

    /**
     * @inheritdoc
     */
    public function doGetTransactions($fromDate, $toDate)
    {
        $fromDate = \DateTime::createFromFormat(self::EXPECTED_DATE_FORMAT, $fromDate);
        $toDate = \DateTime::createFromFormat(self::EXPECTED_DATE_FORMAT, $toDate);

        return $this->doAction(new GetTransactions($fromDate, $toDate));
    }

    /**
     * @inheritdoc
     */
    public function payPhone($phoneNumber, $sum)
    {
        $trimmedPhoneNumber = ltrim($phoneNumber, '+');
        return $this->doAction(new QiwiPayPhone($trimmedPhoneNumber, $sum));
    }

    /**
     * @inheritdoc
     */
    public function payWallet($wallet, $sum)
    {
        $trimmedWallet = ltrim($wallet, '+7');
        return $this->doAction(new QiwiPayWallet($trimmedWallet, $sum));
    }

    /**
     * @inheritdoc
     */
    public function paySelfPhone($sum)
    {
        return $this->doAction(new QiwiPaySelfPhone($sum));
    }

    /**
     * @inheritdoc
     */
    public function payCard($card, $sum)
    {
        return $this->doAction(new QiwiPayCard($card, $sum));
    }

    /**
     * @inheritdoc
     */
    public function getPayConfirm($time, $sum)
    {
        return $this->doAction(new GetPayConfirm($time, $sum));
    }
}