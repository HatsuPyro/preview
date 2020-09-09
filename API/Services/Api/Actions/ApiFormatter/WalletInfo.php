<?php

namespace App\Services\Api\Actions\ApiFormatter;

use App\Models\Wallet;
use App\Services\Tasks\Checker;

/**
 * Класс для форматированного вывода информации о кошельке
 * Class WalletInfo
 * @package App\Services\Api\Actions\ApiFormatter
 */
class WalletInfo
{
    private $wallet = null;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     * Формирует ифнормацию о кошельке в нужном формате
     * @return array
     */
    public function getFormattedInfo()
    {
        $wallet = $this->wallet;
        $settings = $wallet->getSettings();
        $credentials = $wallet->getCredentials();
        $proxyStatus = $wallet->getProxyStatus();

        $result = [
            'wallet_id' => $wallet->id,
            'number' => $wallet->number,
            'name' => $wallet->name,
            'currency' => $wallet->currency,
            'balance' => (float)$wallet->balance,
            'card_number' => $wallet->card_number,
            'payment' => $wallet->getPayment(),
            'active' => $wallet->isActive(),
            'locked' => $wallet->isBlocked(),
            'created' => $wallet->created_at ? $wallet->created_at->format(API_DATE_FORMAT) : null,
            'simbox_id' => $credentials->simbox_id,
            'sim_id' => $credentials->simbox_id,
            'proxy_status' => $proxyStatus,
            'channel' => $wallet->getChannel(),
            'iccid' => $wallet->getIccid(),
            'max_balance' => $settings->getMaxBalance(),
            'list' => $settings->getList(),
            'main' => $wallet->checkMajor() ? true : false,
            'additional' => $wallet->checkAdditional() ? true : false,
            'limits' => [
                'income_daily' => $settings->getIncomeDayLimit(),
                'income_weekly' => $settings->getIncomeWeekLimit(),
                'income_monthly' => $settings->getIncomeMonthLimit(),
            ],
            'turn' => [
                'day' => $wallet->getTurnDay(),
                'week' => $wallet->getTurnWeek(),
                'month' => $wallet->getTurnMonth(),
                'all' => $wallet->getTurnAll(),
                'all_out' => $wallet->getTurnAllOut(),
            ],
            'remainder' => [
                'income_day' => $settings->getIncomeRemainderDay(),
                'income_week' => $settings->getIncomeRemainderWeek(),
                'income_month' => $settings->getIncomeRemainderMonth()
            ],
            'payout' => [
                'payout_type' => $settings->getPayoutType(),
                'payout_wallet' => $settings->payout_wallet,
                'payout_phone' => $settings->payout_phone,
                'payout_card' => $settings->payout_card,
                'min_remainder' => $settings->min_remainder,
                'payout_mode' => $settings->payout_mode,
                'payout_min_sum' => $settings->payout_min_sum,
                'payout_time' => $settings->payout_time
            ],
            'transaction_intervals' => $wallet->getTransactionIntervals()->map(function ($transactionIntervals) {
                $transactionInterval = [
                    'min' => $transactionIntervals->min,
                    'max' => $transactionIntervals->max,
                ];
                return $transactionInterval;
            }),
            'groups' => $wallet->getGroups()->map(function ($group) {
                return $group->getCode();
            })->toArray(),
        ];

        if ($proxyStatus != Wallet::PROXY_STATUS_NONE) {
            $proxy = $wallet->getProxy();
            $result['proxy'] = [
                'ip' => $proxy->ip,
                'port' => $proxy->port,
                'login' => $proxy->login,
                'password' => $proxy->password
            ];
        }

        if (!$wallet->isFullyActive()) {

            $lastErrorLog = $wallet->getLastErrorLog();

            if ($lastErrorLog) {
                $checker = new Checker($lastErrorLog->id);


                $result['error'] = [
                    'errorCode' => $checker->getErrorCode(),
                    'errorMessage' => $lastErrorLog->getComment()
                ];
            }
        }

        return $result;
    }
}
