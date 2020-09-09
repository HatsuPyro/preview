<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/15/19
 * Time: 3:44 PM
 */

namespace Pyrobyte\ApiPayments\Payment\Adgroup\Action;


use phpDocumentor\Reflection\Types\Parent_;
use Pyrobyte\ApiPayments\Entities\Wallet;
use \Pyrobyte\ApiPayments\Payment\Adgroup\Result\GetWallets as Result;

/**
 * Class GetWallets
 * @package Pyrobyte\ApiPayments\Payment\AdgroupQiwi\Action
 */
class GetWallets extends ActionAbstract
{
    const PROTOCOL_INVOICE = 'INVOICE';
    const PROTOCOL_CARD = 'CARD';
    protected $txName = 'fetchWallets';
    protected $url = '/merchant/get-wallet-list';

    /**
     * @inheritdoc
     */
    protected function makeResult($result)
    {
        $walletsArr = $result;
        $wallets = [];
        $walletsArr = array_filter($walletsArr, [$this, 'filter']);
        foreach ($walletsArr as $walletData) {
            $wallet = new Wallet($walletData->tel, $walletData->merchant_user_id, $walletData->rub);
            if ($walletData->protocol_type == 'CARD') {
                $wallet->setCardNumber($walletData->tel);
            }
            $wallets[] = $wallet;

        }
        return new Result($wallets);
    }

    /**
     * Filter incoming wallets
     * @param $wallet
     * @return bool
     */
    protected function filter($wallet)
    {
        if($wallet->protocol_type == self::PROTOCOL_INVOICE || $wallet->protocol_type == self::PROTOCOL_CARD) {
            return false;
        }
        return true;
    }
}