<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 10.04.20
 * Time: 13:33
 */
namespace Pyrobyte\Common;
use GuzzleHttp\Client as GuzzleClient;
use Pyrobyte\Common\Exceptions\ProxyException;
class AbstractGuzzleClient
{
    protected $client = null;
    protected $headers = null;
    function __construct($proxy)
    {
        $this->client = new GuzzleClient(
            [
                'headers' => $this->headers,
                'proxy' => $proxy
            ]
        );
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }

    public function get($url)
    {
        try {
            $response = $this->client->get($url);
        } catch (\Exception $e) {
            if (preg_match('/proxy/imu', $e->getMessage(), $matches)) {
                $proxyException = new ProxyException('Ошибка прокси', 0, $e);
                $proxyException->setProxy($this->proxy);
                throw $proxyException;
            }
            throw new \Exception($e->getMessage());
        }
        return $response;
    }

    public function post($url, $body)
    {
        try {
            $response = $this->client->post($url, $body);
        } catch (\Exception $e) {
            if (preg_match('/proxy/imu', $e->getMessage(), $matches)) {
                $proxyException = new ProxyException('Ошибка прокси', 0, $e);
                $proxyException->setProxy($this->proxy);
                throw $proxyException;
            }
            throw new \Exception($e->getMessage());
        }
        return $response;
    }
}