<?php


namespace App\Extensions\Payment\Adapter\Mkb;

use App\Extensions\Payment\Transaction;
use App\Extensions\Payment\TransactionTranslator as Translator;

/**
 * Class TransactionTranslator
 * @package App\Extensions\Payment\Adapter\Mkb
 */
class TransactionTranslator extends Translator
{
    private $amount;

    public function translate()
    {
        $formatted = [];
        $transaction = $this->initialTransaction;

        foreach ($transaction as $key => $value) {
            switch ($key) {
                case Translator::FIELD_AMOUNT:
                    $this->amount = floatval($value);
                    $value = abs($this->amount);
                    break;
                case Translator::FIELD_DATE:
                    $value = (new \DateTime(str_replace('.', '-', $value)))->format(Translator::DATE_FORMAT);
                    break;
                case Translator::FIELD_DESCRIPTION:
                    break;
                case Translator::FIELD_CURRENCY:
                    $value = strstr($value, 'pyб') ? 'RUR' : $value;
                    break;
                default:
                    continue;
            }

            $formatted[$key] = $value;
        }

        $formatted[self::FIELD_STATUS] = Transaction::STATUS_SUCCESS;
        $formatted[self::FIELD_ID] = hash("sha256",$transaction[self::FIELD_DATE] . '_' . $formatted[self::FIELD_AMOUNT]);

        // type определяем по сумме (положительная или отрицательная). Если суммы нет, то ставим "другая"
        $type = Transaction::TYPE_ANOTHER;
        if ($this->amount) {
            $type = $this->amount < 0 ? Transaction::TYPE_OUTCOME : Transaction::TYPE_INCOME;
        }

        $formatted[self::FIELD_TYPE] = $type;
        $formatted[self::FIELD_ID] = $formatted[self::FIELD_ID] . '';

        $formatted[self::FIELD_COMISSION_CURRENCY] = $formatted[self::FIELD_CURRENCY];

        $formatted['response'] = json_encode($transaction);
        $transactionObject = new Transaction($formatted);

        return $transactionObject;
    }

}