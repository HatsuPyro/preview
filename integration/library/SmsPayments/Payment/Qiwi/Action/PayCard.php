<?php
namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action;


use App\Extensions\Payment\Exception\WrongPaymentCardNumberException;

/**
 * Действие выполнения перевода на банковскую карту
 * Class PayCard
 * @package Pyrobyte\SmsPayments\Payment\Qiwi\Action
 */
class PayCard extends PayAbstract
{
    const START_VISA_CARD = '4';
    const START_MASTERCARD_CARD = '5';
    const START_MIR = '2';

    private $card = null;
    protected $resultClass = \Pyrobyte\SmsPayments\Payment\Qiwi\Result\PayCard::class;


    public function __construct($card, $sum)
    {
        parent::__construct();
        $this->card = $card;
        $this->sum = $sum;
    }

    protected function getMessage()
    {
        //Определяем какая карта была передана Visa или MasterCard
        $firstDigit = substr($this->card, 0, 1);

        if ($firstDigit == self::START_VISA_CARD) {
            $codeInSms = '1963';
        } else if ($firstDigit == self::START_MASTERCARD_CARD) {
            $codeInSms = '21013';
        } else if ($firstDigit == self::START_MIR) {
            $codeInSms = '31652';
        } else {
            throw new WrongPaymentCardNumberException('Номер платежной системы карты не соответствует известным');
        }

        return $codeInSms . ' ' . $this->card . ' ' . $this->sum;
    }
}