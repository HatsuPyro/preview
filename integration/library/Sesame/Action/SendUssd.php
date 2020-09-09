<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.01.2019
 * Time: 14:31
 */

namespace Pyrobyte\Sesame\Action;

use Pyrobyte\Sesame\ActionAbstract;

class SendUssd extends ActionAbstract
{
    protected $url = 'send_ussd';
    protected $resultClass = \Pyrobyte\Sesame\Result\SendUssd::class;
    protected $method = self::METHOD_POST;

    public function __construct($phoneId, $message)
    {
        $this->body = ['from' => $phoneId, 'ussd' => $message];
    }
}