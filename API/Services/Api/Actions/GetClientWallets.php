<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 03.10.2018
 * Time: 14:40
 */

namespace App\Services\Api\Actions;

use App\Entities\Rbac\Resource;
use App\Models\ApiMethod;
use App\Models\MetaPayment;
use App\Models\Payment;
use App\Models\Wallet;
use App\Models\WalletSettings;
use App\Services\Api\ApiResult;
use App\Services\Api\Actions\ApiFormatter\WalletInfo;
use App\Models\TaskLog;

/**
 * Api-метод получения кошельков клиента
 * Class GetClientWallets
 * @package App\Services\Api\Actions
 */
class GetClientWallets extends GetWalletsAbstract
{
    private $apiMethodCode = ApiMethod::CODE_CLIENT_WALLETS;

    public function getWalletsSettingsQuery($query)
    {
        $filtersMap = [
            [ // синоним 'withdrawTo', должен быть стерт с лица земли(вроде как).
                'name' => 'payoutWallet',
                'callback' => function ($value, $query) {

                    $wallets = Wallet::where('number', $value)->get();
                    foreach ($wallets as $wallet) {
                        $query = $query->orwhere('payout_type', '=', WalletSettings::PAYOUT_TYPE_SELF_PHONE)->where('wallet_id', '=', $wallet->id);
                        if (in_array($this->payment, MetaPayment::CODES)) {
                            $codePayments = MetaPayment::getPaymentCode($this->payment);
                            $condition = in_array($wallet->payment, $codePayments);
                        } else {
                            $condition = $wallet->payment == $this->payment;
                        }
                        if ($condition) {
                            $query = $query->orWhere('payout_type', '=', WalletSettings::PAYOUT_TYPE_WALLET)->where('payout_wallet', '=', $value);
                        }
                    }
                    return $query
                        ->orWhere('payout_type', '=', WalletSettings::PAYOUT_TYPE_PHONE)->where('payout_phone', '=', $value)
                        ->orWhere('payout_type', '=', WalletSettings::PAYOUT_TYPE_CARD)->where('payout_card', '=', $value);
                }
            ],
            [
                'name' => 'withdrawTo',
                'callback' => function ($value, $query) {

                    $wallets = Wallet::where('number', $value)->get();
                    foreach ($wallets as $wallet) {
                        $query = $query->orwhere('payout_type', '=', WalletSettings::PAYOUT_TYPE_SELF_PHONE)->where('wallet_id', '=', $wallet->id);
                        if (in_array($this->payment, MetaPayment::CODES)) {
                            $codePayments = MetaPayment::getPaymentCode($this->payment);
                            $condition = in_array($wallet->payment, $codePayments);
                        } else {
                            $condition = $wallet->payment == $this->payment;
                        }
                        if ($condition) {
                            $query = $query->orWhere('payout_type', '=', WalletSettings::PAYOUT_TYPE_WALLET)->where('payout_wallet', '=', $value);
                        }
                    }
                    return $query
                        ->orWhere('payout_type', '=', WalletSettings::PAYOUT_TYPE_PHONE)->where('payout_phone', '=', $value)
                        ->orWhere('payout_type', '=', WalletSettings::PAYOUT_TYPE_CARD)->where('payout_card', '=', $value);
                }
            ],
            [
                'name' => 'payoutType'
            ],
            [
                'name' => 'payoutAccount',
                'callback' => function ($value, $query) {
                    return $query->where(function ($query) use ($value) {

                        $query->where('payout_wallet', '=', $value)
                            ->orWhere('payout_phone', '=', $value)
                            ->orWhere('payout_card', '=', $value);
                    });
                }
            ],
        ];

        $walletsSettingsQuery = WalletSettings::query();

        foreach ($filtersMap as $filter) {
            $filterName = $filter['name'];
            $requestValue = request()->get($filterName);
            if (!empty($requestValue)) {
                if (!empty($filter['callback'])) {
                    $walletsSettingsQuery = $filter['callback']($requestValue, $walletsSettingsQuery);
                } else {
                    $walletsSettingsQuery = $walletsSettingsQuery->where(snake_case($filterName), $requestValue);
                }
                $query = $query->joinSub($walletsSettingsQuery, 'payout_wallets', function ($join) {
                    $join->on('wallets.id', '=', 'payout_wallets.wallet_id');
                })->select('wallets.*');
            }
        }
        return $query;
    }

    public function filters()
    {
        $group = request()->get('group');

        if (!empty($group)) {
            $this->query = $this->query->whereHas('groups', function ($query) use ($group) {
                $query->whereCode($group);
            });
        }
        $payment = $this->payment;
        $cardNumber = request()->get('cardNumber');

        if (!empty($cardNumber)) {
            $this->query = $this->query->where('card_number', $cardNumber);
        }
        $this->query = $this->getWalletsSettingsQuery($this->query);

        $withdrawFrom = request()->get('withdrawFrom');
        if (!empty($withdrawFrom)) {
            $walletsWithdrawFrom = Wallet::where('number', $withdrawFrom)->get();
            $walletsQuery = Wallet::where('id', '<', '0'); // запрос получающий пустое значение
            foreach ($walletsWithdrawFrom as $wallet) {
                $walletSettings = WalletSettings::where('wallet_id', $wallet->id)->get();
                foreach ($walletSettings as $walletSetting) {
                    if ($walletSetting->payout_type == WalletSettings::PAYOUT_TYPE_CARD) {
                        $walletsQuery = $this->query->where('card_number', $walletSetting->payout_card);
                    } elseif ($walletSetting->payout_type == WalletSettings::PAYOUT_TYPE_WALLET) {
                        if (in_array($payment, MetaPayment::CODES)) {
                            $codePayments = MetaPayment::getPaymentCode($payment);
                            $condition = Wallet::where('number', $withdrawFrom)->whereIn('payment', $codePayments)->get()->isEmpty();
                        } else {
                            $condition = Wallet::where('number', $withdrawFrom)->where('payment', $payment)->get()->isEmpty();
                        }
                        if (!$condition) {
                            $walletsQuery = $this->query->where('number', $walletSetting->payout_wallet);
                        }
                    } elseif ($walletSetting->payout_type == WalletSettings::PAYOUT_TYPE_PHONE) {
                        $walletsQuery = $this->query->where('number', $walletSetting->payout_phone);
                    } elseif ($walletSetting->payout_type == WalletSettings::PAYOUT_TYPE_SELF_PHONE) {
                        $walletsQuery = $this->query->where('number', $withdrawFrom);
                    }
                    $this->query = $walletsQuery;
                }
            }
        }


        $hasActiveTasks = request()->get('hasActiveTasks');
        if (!empty($hasActiveTasks)) {
            $walletOperationWorking = Wallet::join('task_logs', 'wallets.id', '=', 'task_logs.wallet_id')->whereIn('task_logs.status', [
                TaskLog::STATUS_STARTED,
                TaskLog::STATUS_PENDING,
                TaskLog::STATUS_CREATED,
            ])->select('wallet_id')->get()->toArray();
            if ($hasActiveTasks == 'true') {
                $this->query = $this->query->whereIn('id', $walletOperationWorking);
            } elseif ($hasActiveTasks == 'false') {
                $this->query = $this->query->whereNotIn('id', $walletOperationWorking);
            }
        }
        $hasCard = request()->get('hasCard');
        if (!empty($hasCard)) {
            if ($hasCard == 'true') {
                $this->query = $this->query->where('card_number', '!=', '');
            } elseif ($hasCard == 'false') {
                $this->query = $this->query->where('card_number', '=', '');
            }
        }

        $isMain = request()->get('isMain');
        if (!empty($isMain)) {
            if ($isMain) {
                $sign = '=';
            } else {
                $sign = '!=';
            }
            $this->query = $this->query->join('wallet_groups', 'wallets.id', '=', 'wallet_groups.wallet_id')
                ->join('payment_groups', 'wallet_groups.group_id', '=', 'payment_groups.group_id')
                ->join('payments', function ($join) use ($sign) {
                    $join->on('payments.id', $sign, 'payment_groups.payment_id')->on('payments.code', '=', 'wallets.payment');
                })->select('wallets.*');
        }
    }

    public function getCode()
    {
        return $this->apiMethodCode;
    }
}