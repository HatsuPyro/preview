<?php

namespace Pyrobyte\ApiPayments\Engine;

abstract class EngineAbstract
{
    public function __construct()
    {
        $this->init();
    }

    abstract public function request(Request $request);
    abstract public function init();
}