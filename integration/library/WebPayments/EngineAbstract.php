<?php

namespace Pyrobyte\WebPayments;

abstract class EngineAbstract
{
    protected $cookies;
    protected $cookiesFile;
    protected $options = [];

    public function getCookies()
    {
        return $this->cookies;
    }

    public function getCookiesFile()
    {
        return $this->cookiesFile;
    }

    public function __construct($cookiesFile = null, $proxy = null)
    {
        $this->cookiesFile = $cookiesFile;
        $this->proxy = $proxy;

        $this->init();
    }

    abstract public function request(Request $request);
    abstract public function init();
}