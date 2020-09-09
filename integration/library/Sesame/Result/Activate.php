<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 08.10.2018
 * Time: 15:45
 */

namespace Pyrobyte\Sesame\Result;


use Pyrobyte\Sesame\ResultAbstract;

class Activate extends ResultAbstract
{
    public function getActivatedSims()
    {
        return $this->response->data;
    }
}