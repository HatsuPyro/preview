<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/26/19
 * Time: 10:07 AM
 */

namespace Pyrobyte\ApiPayments\Exceptions;


class InvalidResponseException extends ApiPaymentException
{
    private $response = null;

    /**
     * @return null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param null $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }
}