<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/18/19
 * Time: 3:16 PM
 */

namespace Pyrobyte\ApiPayments\Payment\Adgroup\Action;

use Pyrobyte\ApiPayments\Config;
use Pyrobyte\ApiPayments\Engine\Request;
use Pyrobyte\ApiPayments\Exceptions\ApiPaymentException;
use Pyrobyte\ApiPayments\Exceptions\InvalidResponseException;

abstract class ActionAbstract extends \Pyrobyte\ApiPayments\Action\ActionAbstract
{
    protected $baseUrl = 'https://api.adgroup.finance';
    protected $url = null;
    protected $txName = null;
    protected $provider = null;

    public function __construct($provider)
    {
        $this->provider = $provider;
    }

    /**
     * Отправка запроса на получение данных
     * @return mixed
     * @throws \Exception
     */
    protected function sendRequest($reqData)
    {
        $request = new Request(
            $this->baseUrl . $this->url,
            Request::METHOD_POST,
            [
                \GuzzleHttp\RequestOptions::JSON => [
                    'header' => [
                        'version' => 0.1,
                        'lang' => 'EN',
                        'txName' => $this->txName,
                    ],
                    'reqData' => $this->txName == 'fetchMerchTx' ? array_merge($reqData, ['provider' => [$this->provider]]) : $reqData
                ],
                'auth' => [
                    $this->client->getAdgroupClientId(),
                    $this->client->getAdgroupClientSecret(),
                ]
            ]
        );
        $request->setHtmlHeaders([
            'Content-type' => 'application/json',
        ]);
        $response = $this->request($request);

        $content = $response->getJsonContent();

        if (!empty($content->errors)) {
            if( strpos($this->getErrorsString($content->errors), 'Malformed basic authentication') !== false) {
                throw new \Exception('Не удалось выполнить синхронизацию с адгруп. Проверьте корректность доступов');
            }
            throw new \Exception('В теле ответа от Adgroup вернулись ошибки: ' . $this->getErrorsString($content->errors));
        }

        $responseData = $content->responseData ?? [];


        return $responseData;
    }

    /**
     * Выполнение запроса
     * @return mixed
     * @throws \Exception
     */
    public function run()
    {
        $result = $this->sendRequest($this->getReqData());
        return $this->makeResult($result);
    }

    /**
     * Сборщик результата запроса
     * @param $result
     * @return mixed
     */
    abstract protected function makeResult($result);

    /**
     * Получение параметров запроса
     * @return array
     */
    protected function getReqData()
    {
        return ['provider' => $this->provider];
    }

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