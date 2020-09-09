<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 1/17/19
 * Time: 3:35 PM
 */

namespace App\Extensions\Payment\Adapter\Qiwi\PyrobyteSmsActions;


use App\Extensions\Payment\Adapter\Result\PayResult;

abstract class PayActionAbstract extends SmsActionAbstract
{
    public function do()
    {
        $clientResult = $this->getClientResult();
        return new PayResult($clientResult->getStatus(), $clientResult->getTime());
    }

    abstract protected function getClientResult();
}