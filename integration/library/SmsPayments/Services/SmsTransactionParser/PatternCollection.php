<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/4/19
 * Time: 11:36 AM
 */

namespace Pyrobyte\SmsPayments\Services\SmsTransactionParser;


class PatternCollection
{
    private $patterns = [];

    public function __construct($patterns)
    {
        $this->patterns = $patterns;
    }
}