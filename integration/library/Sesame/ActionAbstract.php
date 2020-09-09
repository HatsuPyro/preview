<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.09.2018
 * Time: 13:05
 */

namespace Pyrobyte\Sesame;


abstract class ActionAbstract implements ActionInterface
{
    protected $url = null;
    protected $method = self::METHOD_GET;
    protected $result = null;
    protected $resultClass;
    protected $routeParams = null;
    protected $headers = [];
    protected $body = [];

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getUrl()
    {
        return $this->processUrl();
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function call($httpClient, $options)
    {
        $url = $this->getUrl();
        $options['headers'] = array_merge($options['headers'] ?? [], $this->getHeaders());
        if(isset($this->body['from'])  && is_array($this->body['from'])) {
            $this->body['from'] = $this->body['from']['0'];
        }
        $options['body'] = \GuzzleHttp\json_encode(array_merge($options['body'] ?? [], $this->body));
        $options['query'] = $this->routeParams;

        $result = $httpClient->request($this->getMethod(), $url, $options);
        $this->result = $result->getBody()->getContents();
        return $this->getResult();
    }

    public function getResult()
    {
        return new $this->resultClass($this->result);
    }

    /**
     * Заменяет параметры в урле
     * @return mixed|null
     */
    protected function processUrl()
    {
        $url = $this->url;
        $matches = null;

        $resultUrl = $url;
        if(!preg_match('/\{.+\}/', $url, $matches)) {
            return $url;
        }

        $routeParams = &$this->routeParams;
        foreach ($matches as $match) {
            $paramName = str_replace(['{', '}'], '', $match);
            if(array_key_exists($paramName, $routeParams)) {
                $param = array_pull($routeParams, $paramName);
                $resultUrl = str_replace($match, $param, $resultUrl);
            }
        }

        return $resultUrl;
    }

}