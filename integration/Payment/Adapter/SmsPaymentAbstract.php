<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/9/19
 * Time: 4:31 PM
 */

namespace App\Extensions\Payment\Adapter;

use Pyrobyte\SmsPayments\Action\ActionAbstract;
use App\Extensions\Payment\PaymentAbstract;
use \App\Extensions\Payment\Services\SmsTransactionTranslator as Translator;
use App\Extensions\Payment\Adapter\ErrorCatcher\SmsErrorCatcher;

/**
 * Класс-адаптер для работы с смс-либами
 * Содержит в себе общий функционал адаптеров смс-либ
 * Class SmsPaymentAbstract
 * @package App\Extensions\Payment\Adapter
 */
class SmsPaymentAbstract extends PaymentAbstract
{
    protected $client = null;
    protected $clientClass = null;
    protected $getTransactionsActionClass = null;
    protected $state = false;

    public function __construct($params, $tmpPath = null)
    {
        parent::__construct($params, $tmpPath);

        $this->client = new $this->clientClass($params);
    }

    /**
     * Выполняет действие либы и получает его результат
     * @param ActionAbstract $action
     * @return mixed
     * @throws \App\Extensions\Payment\Exception\PaymentException
     */
    protected function doAction(ActionAbstract $action)
    {
        return SmsErrorCatcher::catch(function() use ($action) {
            return $this->client->call($action);
        });
    }

    /**
     * Получение переменной с помощью которой проверяется отключен ли кошелек или нет в лк
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }
//
    /**
     * @inheritdoc
     */
    public function doGetTransactions($fromDate, $toDate)
    {
        $fromDate = \DateTime::createFromFormat(self::EXPECTED_DATE_FORMAT, $fromDate);
        $toDate = \DateTime::createFromFormat(self::EXPECTED_DATE_FORMAT, $toDate);

        // Результат либы
        $libResult = $this->doAction(new $this->getTransactionsActionClass($fromDate, $toDate));
        $transactions = $libResult->getTransactions();
        $resultTransactions = [];
        foreach ($transactions as $transaction) {
            $translator = new Translator($transaction);
            $resultTransactions[] = $translator->translate();
        }

        return $resultTransactions;
    }
}