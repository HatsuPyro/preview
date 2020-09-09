<?php

/**
 * Created by nikita@hotbrains.ru
 * Date: 4/9/19
 * Time: 4:39 PM
 */

namespace Pyrobyte\SmsPayments\Action;

use Pyrobyte\Phone\Result\Entities\Message;
use Pyrobyte\SmsPayments\Entities\Transaction;

abstract class GetTransactionsAbstract extends ActionAbstract
{
    protected $fromDate = null;
    protected $toDate = null;
    protected $resultClass = null;
    protected $transactionPatterns = [];
    protected $transactions = [];

    public function __construct($fromDate, $toDate)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
    }

    /**
     * Получает шаблоны транзакций
     * @return array
     */
    abstract protected function getTransactionPatterns();

    /**
     * @inheritdoc
     */
    public function do()
    {
        $smsMessages = $this->engine->getSms();
        $transactions = &$this->transactions;
        $fromNumbers = $this->getFromNumbers();
        $prevMessages = [];
        foreach ($smsMessages as $message) {
            if (!in_array($message->from, $fromNumbers)) {
                continue;
            }
            if ($message->date < $this->fromDate->getTimestamp() || $message->date > $this->toDate->getTimestamp()) {
                continue;
            }

            $transaction = $this->parseTransaction($message, $prevMessages);
            $prevMessages[] = $message;
            if ($transaction) {
                $transactions[] = $transaction;
            }
        }
        return new $this->resultClass($transactions);
    }

    /**
     * Получает номера телефонов с которых ожидаются сообщения
     * @return mixed
     */
    abstract protected function getFromNumbers();

    /**
     * Пытается получить транзакцию из сообщения
     * @param Message $message
     * @param array $prevMessages
     * @return Transaction|null
     */
    private function parseTransaction(Message $message, array $prevMessages)
    {
        $transactionPatterns = $this->getTransactionPatterns();
        // Массив совпадений регулярки
        $matches = [];
        $search = $this->getSearch();
        $replace = $this->getReplace();
        $transaction = null;
        $maxLengthBalance = 999999;
        foreach ($transactionPatterns as $pattern => $patternProperties) {
            $fullPattern = $pattern;
            if ($search != null && $replace != null) {
                $message->set($this->replace($message->get(), $search, $replace));
            }
            if (preg_match('/' . $fullPattern . '/imu', $message->get(), $matches)) {
                $sum = $this->getSum($message, $matches);
                $transaction = new Transaction($patternProperties['type'], $sum, $message, $message->getDate());
                $transaction->setCardNumber($this->getCard($message->get()));
                $callback = $patternProperties['callback'] ?? null;
                if (is_callable($callback)) {
                    $transaction = $callback($transaction, $message, $prevMessages);
                    // callback является механизмом дополнительных действий над транзакциями
                    // может получиться так, что регулярка сообщения подходит, но через контекст(соседние сообщения)
                    // станет понятно, что это, скажем, незавершенная транзакция и ее пока нельзя возвращать
                    if ($transaction === null) {
                        return $transaction;
                    }
                }
                $balance = $this->getTransactionBalance($message);

                if (($balance !== null) && ($balance < $maxLengthBalance)) {
                    $transaction->setBalance($balance);
                }
                return $transaction;
            }
        }
        return $transaction;
    }

    protected function getReplace()
    {
        return null;
    }

    protected function getSearch()
    {
        return null;
    }

    /**
     * Заменяет определенную часть строки в сообщении, на нужную
     * @param $message
     * @param $search
     * @param $replace
     * @return mixed
     */
    private function replace($message, $search, $replace)
    {
        return str_replace($search, $replace, $message);
    }

    /**
     * Вытаскивает сумму из нужного сообщения и совпадений в основной регулярке
     * @param $message
     * @param $matches - карманы регулярки переданы для того, чтобы по возможности не делать дополнительного поиска в строке
     * @return mixed
     */
    abstract protected function getSum($message, $matches);

    protected function cardPattern()
    {
        return null;
    }

    protected function getCard($message)
    {
        $cardPattern = $this->cardPattern();
        $result = null;
        if ($this->cardNumber && $cardPattern) {
            if (preg_match('/' . $cardPattern . '/imu', $message, $cardMatches)) {
                $cardWallet = substr($this->cardNumber, -4);
                if ($cardMatches[1] == $cardWallet) {
                    $result = $this->cardNumber;
                }
            }

        }
        return $result;
    }

    /**
     * Получает баланс транзакции из сообщения
     * @param Message $message
     * @return mixed
     */
    protected function getTransactionBalance(Message $message)
    {
        return null;
    }
}
