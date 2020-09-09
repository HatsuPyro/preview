<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 14.11.2018
 * Time: 14:31
 */

namespace Pyrobyte\Sesame\Result;


use Pyrobyte\Sesame\ResultAbstract;

class GetUssdResponse extends ResultAbstract
{
    public function getResult()
    {
        return $this->response->data ?? null;
    }

    public function isReady()
    {
        $error = $this->response->error ?? null;
        if(!$error) {
            return true;
        } else {
            return false;
        }
       // $ready = !preg_match('/not.ready/imu', $error);

        //return $ready;
    }

    public function hasError()
    {
        return !empty($this->response->error);
    }

    public function getError()
    {
        return $this->response->error ?? null;
    }
}