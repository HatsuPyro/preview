<?php
/**
 * Движок для работы с платежками через пакет guzzle
 */

namespace Pyrobyte\WebPayments\Engine;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\FileCookieJar;
use Pyrobyte\WebPayments\Config;
use Pyrobyte\WebPayments\Exception\ProxyException;
use Pyrobyte\WebPayments\Request;
use Pyrobyte\WebPayments\Response;

class Guzzle extends \Pyrobyte\WebPayments\EngineAbstract
{
    protected $guzzle;

    public function init()
    {
        $this->cookies = new FileCookieJar($this->cookiesFile, true);

        $params = [
            'cookies' => &$this->cookies,
        ];

        if (!empty($this->proxy)) {
            $params['proxy'] = $this->proxy;
        }

        $this->guzzle = new GuzzleClient($params);
    }

    public function request(Request $request)
    {
        $options = $this->getOptions($request->getHeaders(), $request->getParams());

        try {
            $result = $this->guzzle->request($request->getMethod(), $request->getUrl(), $options);
        } catch (\Exception $e) {
            preg_match('/@((\d+\.?)+)/', $this->proxy, $matches);
            $proxyIp =  $matches[1] ?? null;
            if($proxyIp && stripos($e->getMessage(), $proxyIp)) {
                $proxyException = new ProxyException('Ошибка прокси', 0, $e);
                $proxyException->setProxy($this->proxy);
                throw $proxyException;
            }
            $response = $e->getResponse();
            return new Response(
                $e->getMessage(),
                $response ? $response->getHeaders() : [],
                $e->getCode());
        }

        return new Response(
            $result->getBody()->getContents(),
            $result->getHeaders(),
            $result->getStatusCode());
    }

    public function getOptions(array $headers = [], array $params = [])
    {
        $options = [
            'headers' => $headers,
            'cookies' => $this->cookies,
            'connect_timeout' => Config::getItem('guzzle.connect_timeout'),
        ];

        if (!empty($params)) {
            // Определяем, как отправлять данные по заголовкам
            // TODO: не нравится такой вариант. Необходимо узнавать у реквеста
            if (isset($headers['Content-type']) && (stristr($headers['Content-type'], 'json') !== false)) {
                $options[\GuzzleHttp\RequestOptions::JSON] = $params;

            } elseif (key_exists('additional_options', $params)) {
                $options['form_params'] = $params['form_params'];
                $options = array_merge($options, $params['additional_options']);
            } else {
                $options['form_params'] = $params;
            }
        }

        return $options;
    }


}