<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 1/21/19
 * Time: 6:11 PM
 */

namespace App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions;


use App\Extensions\Payment\Adapter\Result\PayConfirmResult;
use Pyrobyte\SmsPayments\Payment\Qiwi\Action\CheckPay;

/**
 * Адаптер подтверждения платежа для киви смс
 * Class GetPayConfirm
 * @package App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions
 */
class GetPayConfirm extends SmsActionAbstract
{
    private $time = null;
    private $sum = null;

    /**
     * GetPayConfirm constructor.
     * @param $time
     * @param $sum
     */
    public function __construct($time, $sum)
    {
        $this->time = $time;
        $this->sum = $sum;
    }

    /**
     * @inheritdoc
     */
    public function do()
    {
        $result = $this->client->call(new CheckPay($this->time, $this->sum));
        return new PayConfirmResult($result->getStatus());
    }
}