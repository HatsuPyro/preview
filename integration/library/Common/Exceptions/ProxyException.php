<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 10.04.20
 * Time: 15:57
 */
namespace Pyrobyte\Common\Exceptions;

class ProxyException extends \Exception
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