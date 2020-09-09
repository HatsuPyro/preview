<?php

namespace Pyrobyte\WebPayments\Payment;

class Result
{
    protected $response;

    public function __construct($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}