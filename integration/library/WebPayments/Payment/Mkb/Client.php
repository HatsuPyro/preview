<?php


namespace Pyrobyte\WebPayments\Payment\Mkb;

use Pyrobyte\WebPayments\Traits\HasCard;
use Pyrobyte\WebPayments\Traits\Loginable;

class Client extends \Pyrobyte\WebPayments\Payment\ClientAbstract
{
    use Loginable, HasCard;
    protected $simboxId = null;


    public function __construct($account, $password, $simboxId, $card)
    {
        $this->setAccount($account);
        $this->setPassword($password);
        $this->setSimboxId($simboxId);
        $this->setCard($card);
    }

    public function auth()
    {
        $result = $this->call(new \Pyrobyte\WebPayments\Payment\Mkb\Action\Auth());

        return $result->isAuthed();
    }

    public function getCookiesFileName()
    {
        return md5($this->account . $this->password) . '.txt';
    }

    public function setSimboxId($simboxId)
    {
        $this->simboxId = $simboxId;
    }

    public function getSimboxId()
    {
        return $this->simboxId;
    }
}