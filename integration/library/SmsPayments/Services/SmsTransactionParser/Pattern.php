<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/4/19
 * Time: 11:37 AM
 */

namespace Pyrobyte\SmsPayments\Services\SmsTransactionParser;


class Pattern
{
    private $pattern = null;

    public function __construct($pattern)
    {
        $this->pattern = $pattern;
    }
}