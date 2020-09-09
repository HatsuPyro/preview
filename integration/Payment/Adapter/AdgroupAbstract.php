<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 15.11.19
 * Time: 18:58
 */

namespace App\Extensions\Payment\Adapter;


use App\Extensions\Payment\Adapter\ErrorCatcher\ApiErrorCatcher;
use App\Extensions\Payment\Adapter\Result\MassGetBalance;
use App\Extensions\Payment\Entities\Wallet;
use App\Extensions\Payment\Exception\AdgroupException;
use App\Extensions\Payment\Exception\SmsServiceException;
use App\Extensions\Payment\PaymentAbstract;
use App\Models\AdgroupAuthSetting;
use App\Models\SettingsItem;
use Pyrobyte\ApiPayments\Action\ActionAbstract;
use Pyrobyte\ApiPayments\Payment\Adgroup\Action\GetBalance;
use Pyrobyte\ApiPayments\Payment\Adgroup\Action\MassGetTransactions;
use Pyrobyte\ApiPayments\Payment\Adgroup\Client;
use Pyrobyte\ApiPayments\Payment\Adgroup\Action\GetTransactions;
use App\Extensions\Payment\Adapter\AdgroupApi\TransactionTranslator;
use App\Extensions\Payment\Adapter\Result\GetWallets as GetWalletsResult;
use App\Extensions\Payment\Exception\RequiredParamIsMissingException;
use Pyrobyte\ApiPayments\Payment\Adgroup\Action\TransactionFilter\FilterProtocol;

abstract class AdgroupAbstract extends PaymentAbstract
{

    protected $walletNumber = null;
    protected $provider = null;
    protected $filterProtocol = null;
    protected $adgroupId = null;

    public function __construct($params, $tmpPath = null)
    {
        parent::__construct($params, $tmpPath);
        $this->walletNumber = $params['wallet_number'] ?? null;
        $this->adgroupId = $params['adgroup_id'] ?? null;
    }

    protected function doAction(ActionAbstract $action, $adgroupSettings = null)
    {
        if ($adgroupSettings) {
            $client = new Client($adgroupSettings->getClientId(),
                $adgroupSettings->getClientSecret(), $this->provider);
        }
        return ApiErrorCatcher::catch(function () use ($action, $client) {
            return $client->call($action);
        });
    }

    /**
     * @inheritdoc
     */
    public function doGetTransactions($fromDate, $toDate)
    {
        $this->checkRequiredParams(['walletNumber']);

        $fromTimestamp = date_create($fromDate)->getTimestamp();
        $toTimestamp = date_create($toDate)->getTimestamp();
        $getTransactionsAction = new GetTransactions($this->walletNumber, $fromTimestamp, $toTimestamp, $this->provider);
        $getTransactionsAction = $this->addTransactionsFilter($getTransactionsAction);
        $adgroupSetting = AdgroupAuthSetting::where('client_id', $this->adgroupId)->first();
        $translatedTransactions = [];
        if ($adgroupSetting) {
            $adgroupResult = $this->doAction($getTransactionsAction, $adgroupSetting);
            $adgroupTransactions = $adgroupResult->getTransactions();

            foreach ($adgroupTransactions as $adgroupTransaction) {
                $translator = new TransactionTranslator($adgroupTransaction);
                $translatedTransactions[] = $translator->translate();
            }
        } else {
            throw new SmsServiceException('Для данного кошелька не найдены настройки adgroup');
        }

        return $translatedTransactions;
    }

    protected function addTransactionsFilter($getTransactionsAction)
    {
        $getTransactionsAction->setFilters([new FilterProtocol([$this->filterProtocol])]);
        return $getTransactionsAction;
    }

    /**
     * @inheritdoc
     */
    public function getWallets()
    {
        $walletsAction = $this->getWalletsAction();
        $adgroupSettings = AdgroupAuthSetting::all();
        foreach ($adgroupSettings as $adgroupSetting) {
            $adgroupId = $adgroupSetting->getClientId();
            try {
                $qiwiResult = $this->doAction($walletsAction, $adgroupSetting);
            } catch (\Exception $e) {
                throw new AdgroupException('Ошибка синхронизации Adgroup:' . $adgroupId);
            }
            $adgroupWallets = $qiwiResult->getWallets();
            $resultWallets = [];
            foreach ($adgroupWallets as $adgroupWallet) {
                $resultWallets[] = $this->translateWallet($adgroupWallet, $adgroupId);
            }
        }
        $result = new GetWalletsResult(true, $resultWallets);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getBalance($currency = null)
    {
        $adgroupSetting = AdgroupAuthSetting::where('client_id', $this->adgroupId)->first();
        if ($adgroupSetting) {
            $qiwiResult = $this->doAction(new GetBalance($this->walletNumber, $this->provider), $adgroupSetting);
        } else {
            throw new SmsServiceException('Для данного кошелька не найдены настройки adgroup');
        }
        return $qiwiResult->getBalance();
    }

    /**
     * @inheritdoc
     */
    public function massGetBalance()
    {
        $adgroupSettings = AdgroupAuthSetting::all();
        foreach ($adgroupSettings as $adgroupSetting) {
            $adgroupResult = $this->doAction($this->getWalletsAction(), $adgroupSetting);

            $wallets = $adgroupResult->getWallets();
            $balances = [];

            foreach ($wallets as $wallet) {
                $balances[$wallet->getNumber()] = $wallet->getBalance();
            }
        }

        return new MassGetBalance($balances);
    }

    public function massGetTransactions($fromDate, $toDate, $lastTransactionId = null)
    {
        $fromTimestamp = date_create($fromDate)->getTimestamp();
        $toTimestamp = date_create($toDate)->getTimestamp();
        $massGetTransactionsAction = new MassGetTransactions($fromTimestamp, $toTimestamp, $this->provider, $lastTransactionId);
        $massGetTransactionsAction = $this->addMassTransactionsFilter($massGetTransactionsAction);


        $adgroupSettings = AdgroupAuthSetting::all();
        foreach ($adgroupSettings as $adgroupSetting) {
            $adgroupResult = $this->doAction($massGetTransactionsAction, $adgroupSetting);
            $adgroupTransactions = $adgroupResult->getTransactions();

            $translatedTransactions = [];

            foreach ($adgroupTransactions as $adgroupTransaction) {
                $translator = new TransactionTranslator($adgroupTransaction);
                $translatedTransactions[] = $translator->translate();
            }
        }

        return $translatedTransactions;
    }

    protected function addMassTransactionsFilter($massGetTransactionsAction)
    {
        $massGetTransactionsAction->setFilters([new FilterProtocol([$this->filterProtocol])]);
        return $massGetTransactionsAction;
    }

    abstract protected function getWalletsAction();


    /**
     * Переводит Adgroup кошелек в наш
     * @param $adgroupWallet
     * @return Wallet
     */
    protected function translateWallet($adgroupWallet, $adgroupId)
    {
        $wallet = new Wallet($adgroupWallet->getNumber(), $adgroupWallet->getBalance());
        $wallet->setSpecificData(
            [
                'cardNumber' => $adgroupWallet->getCardNumber(),
                'adgroup_id' => $adgroupId,
            ]);
        return $wallet;
    }

    /**
     * Проверяет обязательные параметры метода
     * @param array $requiredParams
     * @return bool
     * @throws RequiredParamIsMissingException
     */
    protected function checkRequiredParams(array $requiredParams)
    {
        foreach ($requiredParams as $param) {
            if (empty($this->{$param})) {
                throw new RequiredParamIsMissingException(
                    'Required param '
                    . strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $param))  // Тут строка преобразуется из camelCase в snake_case
                    . ' missing'
                );
            }
        }

        return true;
    }

}