<?php


namespace Pyrobyte\WebPayments\Payment\Mkb\Result;


class Auth
{
    public $result;

    public function isAuthed()
    {
        return (bool)$this->result;
    }
}