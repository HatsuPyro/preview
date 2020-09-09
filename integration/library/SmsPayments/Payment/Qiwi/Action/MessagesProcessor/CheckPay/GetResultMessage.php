<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 1/21/19
 * Time: 4:22 PM
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\CheckPay;


use Pyrobyte\SmsPayments\Services\MessagesProcessor\MessagesProcessorStateAbstract;

class GetResultMessage extends MessagesProcessorStateAbstract
{
    private $sum = null;
    protected $name = 'Получение сообщения подтверждения';

    /**
     * GetResultMessage constructor.
     * @param null $sum
     */
    public function __construct($sum)
    {
        $this->sum = $sum;
    }

    /**
     * @inheritdoc
     */
    public function doProcess($message)
    {
        //Spisanie c +79006291409 na summu 9.00 rub.
        $pattern = '/Spisanie.*?na.?summu.' . $this->sum . '/imu';
        if(!preg_match($pattern, $message->get())) {
            return null;
        }
        return true;
    }
}