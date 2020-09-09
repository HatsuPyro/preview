<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 14.11.2018
 * Time: 14:27
 */

namespace Pyrobyte\Sesame\Action;


use Pyrobyte\Sesame\ActionAbstract;

class GetUssdResponse extends ActionAbstract
{
    protected $url = 'get_ussd_response';
    protected $resultClass = \Pyrobyte\Sesame\Result\GetUssdResponse::class;
    protected $method = self::METHOD_POST;

    public function __construct($requestId)
    {
        $this->body = ['id' => $requestId];
    }
}