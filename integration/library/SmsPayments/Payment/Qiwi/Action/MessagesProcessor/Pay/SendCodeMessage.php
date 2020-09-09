<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.10.2018
 * Time: 15:10
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor\Pay;


use Pyrobyte\SmsPayments\Config;
use Pyrobyte\SmsPayments\Services\MessagesProcessor\MessagesProcessorStateAbstract;

class SendCodeMessage extends MessagesProcessorStateAbstract
{
    protected $name = 'Отправка кода подтверждения';

    public function doProcess($message)
    {
        $oneTimeCode = $this->getOneTimeCode($message->get());
        if($oneTimeCode) {
            $this->sendOneTimeCode($oneTimeCode);
            return true;
        }

        return null;
    }

    /**
     * Получает одноразовый код подтверждения
     * @param $messageText
     * @return mixed|null
     */
    private function getOneTimeCode($messageText)
    {
        $matches = [];
        //Ваш одноразовый код  4234. Отправьте его в ответ на это SMS
        preg_match('/.*?(код).{0,5}?(\d+)/imu', $messageText, $matches);

        return $matches[2] ?? null;
    }

    /**
     * Отправляет одноразовый код подтверждения
     * @param $code
     * @return mixed
     */
    protected function sendOneTimeCode($code)
    {
        $config = Config::getConfig();
        $to = $config['qiwi_phone'];
        $result = $this->engine->sendSms($to, $code);
        return $result;
    }
}