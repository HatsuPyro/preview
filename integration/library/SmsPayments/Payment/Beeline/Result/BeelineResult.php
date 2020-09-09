<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 09.07.19
 * Time: 15:11
 */

namespace Pyrobyte\SmsPayments\Payment\Beeline\Result;

use Pyrobyte\SmsPayments\Result\ResultAbstract;

class BeelineResult extends ResultAbstract
{
    private $response = null;

    public function __construct($response)
    {
        parent::__construct($response);
        $this->response = $response;
    }

    public function getStatus()
    {
        return (bool)$this->response;
    }
}