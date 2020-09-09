<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 09.07.19
 * Time: 18:54
 */

namespace Pyrobyte\SmsPayments\Payment\Beeline\Action\MessagesProcessor\CheckPay;

use Pyrobyte\SmsPayments\Services\MessagesProcessor\MessagesProcessorStateAbstract;

class GetResultMessage extends MessagesProcessorStateAbstract
{
    protected $name = 'Получение сообщения подтверждения';

    /**
     * @inheritdoc
     */
    public function doProcess($message)
    {
        //Pokupka na summu:.
        $pattern = '/Pokupka na summu:/imu';
        if (!preg_match($pattern, $message->get())) {
            return null;
        }
        return true;
    }

}