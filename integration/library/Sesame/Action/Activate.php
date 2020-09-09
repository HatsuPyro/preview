<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 08.10.2018
 * Time: 15:44
 */

namespace Pyrobyte\Sesame\Action;


use Pyrobyte\Sesame\ActionAbstract;

class Activate extends ActionAbstract
{
    protected $url = 'activate';
    protected $resultClass = \Pyrobyte\Sesame\Result\Activate::class;
    protected $method = self::METHOD_POST;

    public function __construct($phoneId)
    {
        $this->body = ['ids' => [$phoneId]];
    }
}