<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.09.2018
 * Time: 13:07
 */

namespace Pyrobyte\Sesame\Action;


use Pyrobyte\Sesame\ActionAbstract;

class SendMessage extends ActionAbstract
{
    protected $url = 'send_sms';
    protected $resultClass = \Pyrobyte\Sesame\Result\SendMessage::class;
    protected $method = self::METHOD_POST;

    public function __construct($phoneId, $phoneNumber, $message)
    {
        $this->body = ['from' => $phoneId, 'message' => $message, 'to' => $phoneNumber];
    }
}