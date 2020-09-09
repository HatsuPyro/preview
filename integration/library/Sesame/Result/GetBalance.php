<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 14.11.2018
 * Time: 13:54
 */

namespace Pyrobyte\Sesame\Result;


use Pyrobyte\Sesame\Exceptions\NoResultException;
use Pyrobyte\Sesame\ResultAbstract;

class GetBalance extends ResultAbstract
{
    private $requestId = null;

    public function __construct($response)
    {
        parent::__construct($response);

        if(!isset($this->response->data)) {
            throw new NoResultException('При получении баланса сим-карты не был получен результат операции');
        }
        $this->requestId = $this->response->data;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }
}