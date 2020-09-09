<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.09.2018
 * Time: 15:57
 */

namespace Pyrobyte\Sesame\Action;


use Pyrobyte\Sesame\ActionAbstract;

class GetMessages extends ActionAbstract
{
    protected $url = 'get_sms_list';
    protected $resultClass = \Pyrobyte\Sesame\Result\GetMessages::class;

    public function __construct($phoneId)
    {
        $this->routeParams['sim'] = $phoneId;
    }
}