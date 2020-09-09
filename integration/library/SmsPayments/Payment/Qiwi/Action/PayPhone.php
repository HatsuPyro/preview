<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 17.10.2018
 * Time: 16:19
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action;


class PayPhone extends PayAbstract
{
    private $phoneNumber = null;
    protected $resultClass = \Pyrobyte\SmsPayments\Payment\Qiwi\Result\PayPhone::class;


    public function __construct($phoneNumber, $sum)
    {
        parent::__construct();
        $this->phoneNumber = $phoneNumber;
        $this->sum = $sum;
    }

    protected function getMessage()
    {
        return $this->phoneNumber . ' ' . $this->sum;
    }
}