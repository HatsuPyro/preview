<?php

namespace Pyrobyte\WebPayments;

class Response
{
    protected $content;
    protected $headers;
    protected $code;

    public function __construct($content, $headers, $code)
    {
        $this->content = $content;
        $this->headers = $headers;
        $this->code = $code;
    }

    /**
     * Возвращает тело выполнения запроса в виде объекта. Если ошибка, то null
     * @return string|null
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Возвращает json контент
     * @return mixed
     */
    public function getJsonContent()
    {
        return json_decode($this->getContent());
    }

    /**
     * Возвращает массив заголовков ответа
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Возвращает код ответа
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }
}