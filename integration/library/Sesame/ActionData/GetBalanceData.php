<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 6/24/19
 * Time: 3:51 PM
 */

namespace Pyrobyte\Sesame\ActionData;


class GetBalanceData
{
    private $simId = null;
    private $operator = null;

    /**
     * @return null
     */
    public function getSimId()
    {
        return $this->simId;
    }

    /**
     * @param null $simId
     * @return self
     */
    public function setSimId($simId): self
    {
        $this->simId = $simId;
        return $this;
    }

    /**
     * @return null
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param null $operator
     * @return self
     */
    public function setOperator($operator): self
    {
        $this->operator = $operator;
        return $this;
    }
}