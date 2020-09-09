<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 1/17/19
 * Time: 3:14 PM
 */

namespace App\Extensions\Payment\Adapter\Result;

/**
 * Класс результата платежа
 * Class PayResult
 * @package App\Extensions\Payment\Adapter\Result
 */
class PayResult extends ResultAbstract
{
    private $time = null;

    public function __construct($status, $time)
    {
        parent::__construct($status);
        $this->time = $time;
    }

    /**
     * @return null
     */
    public function getTime()
    {
        return $this->time;
    }


}