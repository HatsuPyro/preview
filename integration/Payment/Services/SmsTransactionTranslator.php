<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/9/19
 * Time: 10:37 AM
 */

namespace App\Extensions\Payment\Services;

use App\Extensions\Payment\Transaction;
use App\Extensions\Payment\TransactionInterface;
use \App\Extensions\Payment\TransactionTranslator as BaseTranslator;
use Pyrobyte\SmsPayments\Entities\Transaction as InitialTransaction;

class SmsTransactionTranslator extends BaseTranslator
{
    public function translate()
    {
        $state = false;
        $initialTransaction = $this->initialTransaction;
        $stateLock = $initialTransaction->getStateLock();
        if ($stateLock) {
            $state = 'notActive';
        }
        $typeMap = [
            InitialTransaction::TYPE_INCOME => TransactionInterface::TYPE_INCOME,
            InitialTransaction::TYPE_OUTCOME => TransactionInterface::TYPE_OUTCOME,
        ];
        $translatedTransactionFields = [
            self::FIELD_DATE => date(self::DATE_FORMAT, $initialTransaction->getDate()),
            self::FIELD_AMOUNT => $initialTransaction->getSum(),
            self::FIELD_PAYER => $initialTransaction->getPayer(),
            self::FIELD_TYPE => $typeMap[$initialTransaction->getType()],
            self::FIELD_ID => md5(
                $initialTransaction->getType()
                . $initialTransaction->getDate()
                . $initialTransaction->getSum()
            ),
            self::FIELD_BALANCE => $initialTransaction->getBalance(),
            self::FIELD_STATE => $state,
            self::FIELD_CARD_NUMBER => $initialTransaction->getCardNumber()
        ];

        return new Transaction($translatedTransactionFields);
    }
}