<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/4/19
 * Time: 11:35 AM
 */

namespace Pyrobyte\SmsPayments\Services\SmsTransactionParser;


class SmsTransactionParser
{
    /**
     * @var PatternCollection
     */
    private $patternCollection = null;

    public function __construct(PatternCollection $patternCollection)
    {
        $this->patternCollection = $patternCollection;
    }
}