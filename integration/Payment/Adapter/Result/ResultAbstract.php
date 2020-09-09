<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 1/17/19
 * Time: 3:12 PM
 */

namespace App\Extensions\Payment\Adapter\Result;


abstract class ResultAbstract
{
    protected $status = null;

    public function __construct($status)
    {
        $this->status = $status;
    }

    /**
     * @return null
     */
    public function getStatus()
    {
        return $this->status;
    }
}