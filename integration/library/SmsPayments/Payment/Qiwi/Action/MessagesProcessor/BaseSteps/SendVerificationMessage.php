<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.10.2018
 * Time: 13:52
 */

namespace Pyrobyte\SmsPayments\Payment\Qiwi\Action\MessagesProcessor;


use Pyrobyte\SmsPayments\Config;
use Pyrobyte\SmsPayments\Entities\Provider;
use Pyrobyte\SmsPayments\Services\MessagesProcessor\MessagesProcessorStateAbstract;

class SendVerificationMessage extends MessagesProcessorStateAbstract
{
    //Для подтверждения заказа услуги за 4 руб. от ООО \"СМС сервисы\" отправьте ДА или пустое сообщение.
    //Отправьте 1 в ответном SMS для подтверждения заказа услуги. Цена услуги 4 р. за каждое SMS. Провайдер ООО Информпартнер. Справочно-информационная-развлекательная SMS услуга. Списание с основного счета.
    protected $verificationDemandPattern = '/Для подтверждения.*ДА/imu'; // Для смс другой формат сообщения
    protected $verificationMessage = 'ДА';
    protected $name = 'Сообщение подтверждения';

    public function preProcess()
    {
        if($this->provider == Provider::MEGAFON) {
            $this->verificationDemandPattern = '/Отправьте 1 в ответном.*/imu';
            $this->verificationMessage = '1';
        }
    }

    public function doProcess($message)
    {
        $needSend = preg_match($this->verificationDemandPattern, $message->get());
        if(!$needSend) {
            return null;
        }
        $config = Config::getConfig();
        $to = $config['qiwi_phone'];
        $this->engine->sendSms($to, $this->verificationMessage);
        return true;
    }
}