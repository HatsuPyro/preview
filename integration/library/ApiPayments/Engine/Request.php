<?php

namespace Pyrobyte\ApiPayments\Engine;

class Request
{
    protected $url;
    protected $method;
    protected $params;
    protected $headers = [];
    protected $userAgent = 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.92 Safari/537.36';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    public function __construct($url, $method, array $params = [])
    {
        $this->url = $url;
        $this->method = $method;
        $this->params = $params;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setHeaders(array $headers = [])
    {
        $this->headers = $headers;
    }

    public function setHtmlHeaders(array $headers = [])
    {
        $this->setHeaders(array_merge([
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,fr;q=0.6,it;q=0.5,th;q=0.4',
            'Connection' => 'keep-alive',
            'User-Agent' => $this->userAgent,
            'Upgrade-Insecure-Requests' => '1',
        ], $headers));
    }
}