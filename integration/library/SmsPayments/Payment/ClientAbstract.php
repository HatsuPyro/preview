<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.10.2018
 * Time: 14:32
 */

namespace Pyrobyte\SmsPayments\Payment;


use Pyrobyte\SmsPayments\Action\ActionAbstract;
use Pyrobyte\SmsPayments\Engine\PhoneService;
use Pyrobyte\SmsPayments\Exceptions\EmptyPhoneIdException;
use Pyrobyte\SmsPayments\Exceptions\EmptySimProviderException;

class ClientAbstract
{
    protected $engine = null;
    protected $phoneId = null;
    protected $walletNumber = null;
    protected $provider = null;
    protected $currency = null;
    protected $cardNumber = null;

    public function __construct($params = [])
    {
        $this->checkRequiredParams($params);

        $this->phoneId = $params['simbox_id'];
        $this->provider = $params['provider'];
        $this->currency = $params['currency'];
        $this->cardNumber = $params['card_number'];

        $this->initEngine();
    }

    /**
     * Инициализирует движок
     * @return $this
     */
    protected function initEngine()
    {
        $this->engine = new PhoneService($this->phoneId);
        return $this;
    }

    /**
     * Вызывает действие
     * @param ActionAbstract $action
     * @return mixed
     */
    public function call(ActionAbstract $action)
    {
        $action->setEngine($this->engine);
        $action->setProvider($this->provider);
        $action->setCurrency($this->currency);
        $action->setCardNumber($this->cardNumber);

        $result = $action->do();
        return $result;
    }

    private function checkRequiredParams($params = [])
    {
        $requiredParams = [
            'simbox_id' => [
                'exception' => EmptyPhoneIdException::class,
                'text' => 'Phone id not specified. Nothing to work with.',
            ],
            'provider' => [
                'exception' => EmptySimProviderException::class,
                'text' => 'Sim provider needs to be specified.',
            ],
        ];

        foreach ($requiredParams as $code => $error) {
            if(empty($params[$code])) {
                $exceptionClass = $error['exception'];
                $exceptionText = $error['text'];
                throw new $exceptionClass($exceptionText);
            }
        }
    }
}