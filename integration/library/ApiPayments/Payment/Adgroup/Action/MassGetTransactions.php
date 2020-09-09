<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/4/19
 * Time: 5:18 PM
 */

namespace Pyrobyte\ApiPayments\Payment\Adgroup\Action;


use Pyrobyte\ApiPayments\Config;
use Pyrobyte\ApiPayments\Entities\Transaction;
use Pyrobyte\ApiPayments\Exceptions\ApiPaymentException;
use Pyrobyte\ApiPayments\Payment\Adgroup\Result\MassGetTransactions as Result;

class MassGetTransactions extends ActionAbstract
{
    private $fromDate = null;
    private $toDate = null;
    private $lastTransactionId = null;
    private $filters = [];

    protected $txName = 'fetchMerchTx';
    protected $url = '/transfer/get-merchant-tx';

    public function __construct(int $fromTime, int $toTime, $provider, $lastTransactionId = null)
    {
        $this->fromDate = $fromTime;
        $this->toDate = $toTime;
        $this->lastTransactionId = $lastTransactionId;
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

        $transactions = [];

        $offset = 0;
        // Делаем ли мы сейчас последний запрос
        $isLastAttempt = false;
        // Общий лимит транзакций, больше которого не получаем
        $totalLimit = Config::getItem('adgroup.mass_update.total_count');

        // @todo убрать дублирование с получением транзакций для одного кошелька
        do {
            $limit = 200;
            $reqData = [
                'limit' => $limit,
                'start' => $offset,
                "universal" => 1,
            ];
            $reqData = array_merge($reqData, $this->filters);
            $currentRequestResult = $this->sendRequest($reqData);

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
                if($transaction->_id == $this->lastTransactionId) {
                    $isLastAttempt = true;
                }

                if($this->lastTransactionId == null) {
                    $isLastAttempt = true;
                }

                return $time >= $this->fromDate && $time <= $this->toDate;
            });
            $transactions = array_merge($transactions, $filteredTransactions);
        } while (
            $portionSize <= $limit
            && !$isLastAttempt
            && (($limit + $offset) <= $totalLimit)
        );

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