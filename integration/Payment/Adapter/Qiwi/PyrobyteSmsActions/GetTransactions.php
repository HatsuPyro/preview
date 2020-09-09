<?php
namespace App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions;

use \Pyrobyte\SmsPayments\Payment\Qiwi\Action\GetTransactions as QiwiAction;
use App\Extensions\Payment\Services\SmsTransactionTranslator as Translator;

/**
 * Адаптер для операции получения киви транзакций из смс оповещений
 * Class GetTransactions
 * @package App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions
 */
class GetTransactions extends SmsActionAbstract
{
    public $fromDate;
    public $toDate;


    public function __construct($fromDate, $toDate)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    public function do()
    {
        $result = $this->client->call(new QiwiAction($this->fromDate, $this->toDate));

        $transactions  = $result->getTransactions();

        $formattedTransactions = [];

        foreach ($transactions as $transaction) {
            $translator = new Translator($transaction);
            $formattedTransactions[] = $translator->translate();
        }

        //вынести в конфиг отключение сохранения транзакций
        return $formattedTransactions;
    }
}