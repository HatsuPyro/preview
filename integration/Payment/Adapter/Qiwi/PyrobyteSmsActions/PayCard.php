<?php
namespace App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions;

use Pyrobyte\SmsPayments\Payment\Qiwi\Action\PayCard as QiwiPayAction;

/**
 * Адаптер вывода с киви на банковскую карту
 * Class PayCard
 * @package App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions
 */
class PayCard extends PayActionAbstract
{
    private $card = null;
    private $sum = null;

    public function __construct($card, $sum)
    {
        $this->card = $card;
        $this->sum = $sum;
    }

    protected function getClientResult()
    {
        $result = $this->client->call(new QiwiPayAction($this->card, $this->sum));
        return $result;
    }

}