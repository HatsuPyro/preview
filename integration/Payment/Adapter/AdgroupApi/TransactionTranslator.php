<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/18/19
 * Time: 6:19 PM
 */

namespace App\Extensions\Payment\Adapter\AdgroupApi;

use App\Extensions\Payment\Logger;
use App\Extensions\Payment\TransactionInterface;
use App\Extensions\Payment\TransactionTranslator as BaseTranslator;
use Pyrobyte\ApiPayments\Entities\Transaction;
use App\Extensions\Payment\Transaction as ResultTransaction;

/**
 * Класс преобразователь информации транзакций к общему виду
 * Class TransactionTranslator
 * @package App\Extensions\Payment\Adapter\AdgroupApi
 */
class TransactionTranslator extends BaseTranslator
{
    public function translate()
    {
        $initialTransaction = $this->initialTransaction;

        $statusMap = [
            Transaction::STATUS_APPROVED => TransactionInterface::STATUS_SUCCESS,
        ];
        $status = $statusMap[$initialTransaction->getStatus()] ?? null;
        $logger = Logger::getInstance();

        if(is_null($status)) {
            $logger->addMessage('Неизвестный статус транзакции. Транзакция: ' . serialize($initialTransaction), Logger::TYPE_UNKNOWN_RESPONSE);
            $status = TransactionInterface::STATUS_UNKNOWN;
        }

        $typeMap = [
            Transaction::TYPE_INCOME => TransactionInterface::TYPE_INCOME,
            Transaction::TYPE_OUTCOME => TransactionInterface::TYPE_OUTCOME,
        ];

        $type = $typeMap[$initialTransaction->getType()] ?? null;
        if(is_null($type)) {
            $logger->addMessage('Неизвестный тип транзакции. Транзакция: ' . serialize($initialTransaction), Logger::TYPE_UNKNOWN_RESPONSE);
            $type = TransactionInterface::TYPE_ANOTHER;
        }

        $formattedTransaction = [
            self::FIELD_AMOUNT => $initialTransaction->getAmount(),
            self::FIELD_CURRENCY => $initialTransaction->getCurrency() == 'RUB' ? 'RUR' : $initialTransaction->getCurrency(),
            self::FIELD_DATE => date(self::DATE_FORMAT, $initialTransaction->getTime()),
            self::FIELD_ID => $initialTransaction->getId(),
            self::FIELD_STATUS => $status,
            self::FIELD_TYPE => $type,
            self::FIELD_PAYER => $initialTransaction->getPayer(),
            self::FIELD_DESTINATION => $initialTransaction->getDestination(),
            self::FIELD_DESCRIPTION => $initialTransaction->getNote(),
        ];
        $resultClass = new ResultTransaction($formattedTransaction);
        return $resultClass;
    }
}