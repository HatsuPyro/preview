<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 5/29/19
 * Time: 1:26 PM
 */

namespace App\Extensions\Payment\Exception;


class ProxyException extends PaymentException
{
    private $proxy = null;

    public function setProxy(string $proxy)
    {
        $this->proxy = $proxy;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProxy():? string
    {
        return $this->proxy;
    }

}