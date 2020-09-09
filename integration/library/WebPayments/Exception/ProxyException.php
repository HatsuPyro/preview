<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 5/29/19
 * Time: 1:17 PM
 */

namespace Pyrobyte\WebPayments\Exception;


class ProxyException extends Exception
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