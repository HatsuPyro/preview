<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 03.10.2018
 * Time: 14:40
 */

namespace App\Services\Api\Actions;

use App\Entities\Rbac\Resource;
use App\Models\ApiMethod;
use App\Models\Wallet;
use App\Services\Api\ApiResult;
use Illuminate\Support\Carbon;

/**
 * Api-метод получения истории транзакций кошелька
 * Class GetWalletHistory
 * @package App\Services\Api\Actions
 */
class GetWalletHistory extends WalletActionAbstract
{
    protected $resource = Resource::WALLETS;

    public function do()
    {
        $request = request();

        $validationRules = [
            'rows' => 'nullable|numeric',
            'startDate' => 'date_format:' . API_DATE_FORMAT,
            'endDate' => 'date_format:' . API_DATE_FORMAT,
            'currency' => 'in:RUR,USD,EUR',
            'payer' => 'string',
        ];

        $this->validate($request, $validationRules);

        $wallet = $this->wallet;

        $transactionsQuery = $wallet->transactions();
        $filterFields = ['rows', 'currency', 'operation', 'startDate', 'endDate', 'payer'];

        foreach ($filterFields as $field) {
            $value = $request->get($field);
            if ($value) {
                $transactionsQuery = $this->addHistoryFilter($transactionsQuery, $field, $value);
            }
        }

        $transactions = $transactionsQuery->get();

        $apiResult = new ApiResult([
            'wallet' => $wallet->number,
            'transactions' => $transactions,
        ]);
        $apiResult->setApiCode(ApiMethod::CODE_GET_WALLETS_TRANSACTIONS);
        return $apiResult;
    }

    /**
     * Добавляет фильтр на запрос по указанным параметрам
     * @param $query
     * @param $field
     * @param $value
     * @return mixed
     */
    protected function addHistoryFilter($query, $field, $value)
    {
        switch ($field) {
            case 'rows':
                $query->limit($value);
                break;
            case 'payer':
                $query->where('payer', $value);
                break;
            case 'currency':
                $query->where('sum_currency', $value);
                break;
            case 'operation':
                $query->where('type', $value);
                break;
            case 'startDate':
                $value = Carbon::createFromFormat(API_DATE_FORMAT, $value);
                $query->where('date', '>', $value);
                break;
            case 'endDate':
                $value = Carbon::createFromFormat(API_DATE_FORMAT, $value);
                $query->where('date', '<', $value);
                break;
        }

        return $query;
    }
}
