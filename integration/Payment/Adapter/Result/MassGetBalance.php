<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/4/19
 * Time: 4:21 PM
 */

namespace App\Extensions\Payment\Adapter\Result;


class MassGetBalance extends ResultAbstract
{
    private $balances = [];

    /**
     * MassGetBalance constructor.
     * @param array $balances
     */
    public function __construct(array $balances)
    {
        $this->balances = $balances;
    }

    /**
     * @return array
     */
    public function getBalances(): array
    {
        return $this->balances;
    }
}