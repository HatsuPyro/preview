<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 18.11.19
 * Time: 13:26
 */

namespace Pyrobyte\ApiPayments\Payment\P2P\Action;

use Pyrobyte\ApiPayments\Engine\Request;

abstract class ActionAbstract extends \Pyrobyte\ApiPayments\Action\ActionAbstract
{
    protected $baseUrl = 'https://p2p.cx/api';
    protected $apiKey = null;
    protected $url = null;
    protected $resultClass = null;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Выполнение запроса
     * @return mixed
     * @throws \Exception
     */
    public function run()
    {
        $this->setUrl();
        $request = new Request(
            $this->baseUrl . $this->url,
            Request::METHOD_GET,
            []
        );
        $response = $this->request($request);

        $content = $response->getJsonContent();

        if (!empty($content->status) && $content->status == 'error') {

            if($content->message == 'wallet not found') {
                throw new \Exception('В теле ответа от P2P вернулись ошибки: Кошелька нет в системе');
            }

            if($content->message == 'api key not valid') {
                throw new \Exception('В теле ответа от P2P вернулись ошибки: API ключ недействителен');
            }
            throw new \Exception('В теле ответа от P2P вернулись ошибки: ' . $content->message);
        }

        return new $this->resultClass($content);
    }

    /**
     * Устанавливает url
     * @return mixed
     */
    abstract protected function setUrl();


    /**
     * Получает строку с ошибками из ответа на запрос
     * @param array $errors
     * @return mixed|string
     */
    protected function getErrorsString($errors = [])
    {
        $errorString = '';
        foreach ($errors as $error) {
            $errorString .= $error->message . '.';
        }
        return $errorString;
    }

}