<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/15/19
 * Time: 3:48 PM
 */

namespace Pyrobyte\ApiPayments\Engine;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\FileCookieJar;
use Pyrobyte\ApiPayments\Logger;

/**
 * Class Guzzle
 * @package Pyrobyte\ApiPayments\Engine
 */
class Guzzle extends EngineAbstract
{
    protected $guzzle;

    public function init()
    {
        $this->guzzle = new GuzzleClient();
    }

    public function request(Request $request)
    {
        $options = $this->getOptions($request->getParams());
        $logger = Logger::getInstance();
        try {
            $logger->logSentRequest($request);
            $result = $this->guzzle->request($request->getMethod(), $request->getUrl(), $options);
            $content = $result->getBody()->getContents();
            $headers = $result->getHeaders();
            $status = $result->getStatusCode();
        } catch (\Exception $e) {
            $response = $e->getResponse();
            $content = $e->getMessage();
            $headers = $response ? $response->getHeaders() : [];
            $status = $e->getCode();
        }
        $response = new Response($content, $headers, $status);
        $logger->logResponse($response);
        sleep(1);

        return $response;
    }

    public function getOptions(array $params = [])
    {
        $options = array_merge($params, [
            'connect_timeout' => 15,
        ]);

        return $options;
    }
}