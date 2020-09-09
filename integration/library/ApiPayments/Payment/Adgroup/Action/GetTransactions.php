<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/18/19
 * Time: 5:37 PM
 */

namespace Pyrobyte\ApiPayments\Payment\Adgroup\Action;

use Pyrobyte\ApiPayments\Entities\Transaction;
use Pyrobyte\ApiPayments\Exceptions\ApiPaymentException;
use Pyrobyte\ApiPayments\Payment\Adgroup\Result\GetTransactions as Result;

class GetTransactions extends ActionAbstract
{
    private $fromDate = null;
    private $toDate = null;
    private $walletNumber = null;
    protected $txName = 'fetchMerchTx';
    protected $url = '/transfer/get-merchant-tx';
    private $filters = [];

    public function __construct($walletNumber, int $fromTime, int $toTime, $provider)
    {
        $this->fromDate = $fromTime;
        $this->toDate = $toTime;
        $this->walletNumber = $walletNumber;
        parent::__construct($provider);
    }

    public function setFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $this->filters = array_merge($this->filters, $filter->get());
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        // this are filters by wallet number: where it is destination and source
        $walletFiltersMap = [
            'dest_address',
            'source_address',
        ];
        $transactions = [];

        foreach ($walletFiltersMap as $filter) {
            $offset = 0;
            $limit = 200;
            $isLastAttempt = false;
            do {
                $requestParams = array_merge([
                    'limit' => $limit,
                    'start' => $offset,
                    "universal" => 1,
                    $filter => [$this->walletNumber],
                ], $this->filters);
                $currentRequestResult = $this->sendRequest($requestParams);

                // т.к адгруп возвращает 403 при частых запросах
                if (empty($currentRequestResult)) {
                    $e = new ApiPaymentException('Невалидный ответ от Adgroup');
                    throw $e;
                }

                $transactionsPortion = $currentRequestResult->transactions;
                $portionSize = count($transactionsPortion);
                $offset += $portionSize;

                $filteredTransactions = array_filter($transactionsPortion, function ($transaction) use (&$isLastAttempt) {
                    $time = date_create($transaction->ctime)->getTimestamp();
                    // Если время транзакции меньше нижней границы - дальше транзакции нет смысла запрашивать, т.к. дальше идут уже более ранние транзакции
                    if ($this->fromDate > $time) {
                        $isLastAttempt = true;
                    }
                    return $time >= $this->fromDate && $time <= $this->toDate;
                });
                $transactions = array_merge($transactions, $filteredTransactions);
            } while ($portionSize >= $limit && !$isLastAttempt);
        }

        return $this->makeResult($transactions);
    }

    /**
     * Convert adgroup transaction data to lib data
     * @param $transactionData
     * @return array
     */
    private function convertTransactionFields($transactionData)
    {
        $fieldsArr = [
            '_id' => 'id',
            'tx_type' => 'type',
            'tx_status' => 'status',
            'ctime' => 'time',
            'source_address' => 'payer',
            'dest_address' => 'destination',
        ];

        $finalTransaction = [];

        foreach ($transactionData as $field => $value) {
            $finalField = null;
            $finalField = $fieldsArr[$field] ?? $field;
            if ($field == 'note') {
                $matches = [];
                if (preg_match('/(?<=:).*/i', $value, $matches)) {
                    if ($matches[0] == 'null') {
                        $value = null;
                    } else {
                        $value = $matches[0];
                    }
                }
            }
            $finalTransaction[$finalField] = $value;
        }

        return $finalTransaction;
    }

    /**
     * @inheritdoc
     */
    protected function makeResult($result)
    {
        $transactions = [];
        foreach ($result as $transactionArr) {
            $convertedTransaction = $this->convertTransactionFields($transactionArr);
            $transactions[] = new Transaction($convertedTransaction);
        }

        return new Result($transactions);
    }
}