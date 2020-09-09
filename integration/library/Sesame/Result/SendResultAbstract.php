<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 18.01.2019
 * Time: 14:35
 */

namespace Pyrobyte\Sesame\Result;

use Pyrobyte\Sesame\Exceptions\NoResultException;
use Pyrobyte\Sesame\ResultAbstract;

abstract class SendResultAbstract extends ResultAbstract
{
    protected $ussd;
    protected $itemName = '';

    public function __construct($response)
    {
        parent::__construct($response);

        if(empty($this->response->data)) {
            throw new NoResultException('При попытке отправить ' . $this->itemName .' не был получен результат');
        }
        $this->ussd = $this->response->data;
    }

    /**
     * @return bool
     */
    public function getStatus()
    {
        return (bool)$this->ussd;
    }

    /**
     * @return string
     */
    public function getRequestId()
    {
        return $this->ussd;
    }

    protected function setValidationRules()
    {
        $rules = [
            'data' => 'string',
        ];

        $this->rules = $rules;
    }
}