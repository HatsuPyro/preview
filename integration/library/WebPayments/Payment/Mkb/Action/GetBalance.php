<?php


namespace Pyrobyte\WebPayments\Payment\Mkb\Action;

use Pyrobyte\WebPayments\Request;

class GetBalance extends \Pyrobyte\WebPayments\Payment\ActionAbstract
{
    public $resultClass = '\Pyrobyte\WebPayments\Payment\Mkb\Result\GetBalance';

    private $stepTwoUrl = '';
    private $fromDate = '';
    private $toDate = '';

    public function run()
    {
        $this->fromDate = now()->subMonth(1)->format('d.m.Y');
        $this->toDate = now()->format('d.m.Y');

        $this->stepOne();
        $this->stepTwo();
        $response = $this->stepThree();
        $balance = $this->getBalance($response->getContent());
        $resultClass = new $this->resultClass($response);
        $resultClass->balance = $balance;
        return $resultClass;
    }

    public function stepOne()
    {
        $request = new Request(
            'https://online.mkb.ru/secure/main.aspx',
            Request::METHOD_GET,
            []
        );

        $request->setHtmlHeaders([
            'Host' => 'online.mkb.ru',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Connection' => 'keep-alive',
            'Referer' => 'https://online.mkb.ru/secure/login.aspx?a=2&returnurl=',
            'TE' => 'Trailers'
        ]);
        $response = $this->request($request);
        preg_match('/(\/secure\/cops\.aspx\?id.+)">/U', $response->getContent(), $matches);
        if (!isset($matches[1])) {
            throw new \Exception('В ответе нет ссылки получения баланса');
        }
        $this->stepTwoUrl = $matches[1];
    }

    public function stepTwo()
    {
        $request = new Request(
            'https://online.mkb.ru' . $this->stepTwoUrl,
            Request::METHOD_GET,
            []
        );

        $request->setHtmlHeaders([
            'Host' => 'online.mkb.ru',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Connection' => 'keep-alive',
            'Referer' => 'https://online.mkb.ru/secure/main.aspx',
            'TE' => 'Trailers'
        ]);
        $this->request($request);
    }

    public function stepThree()
    {
        $request = new Request(
            'https://online.mkb.ru/secure/cardoperations.aspx/GetHeaders',
            Request::METHOD_POST,
            [
                'df' => $this->fromDate,
                'dt' => $this->toDate,
            ]
        );

        $request->setHtmlHeaders([
            'Host' => 'online.mkb.ru',
            'Accept' => 'application/json, text/javascript, */*; q=0.01',
            'Accept-Language' => 'ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
            'Content-type' => 'application/json; charset=utf-8',
            'X-Requested-With' => 'XMLHttpRequest',
            'Content-Length' => '35',
            'Origin' => 'https://online.mkb.ru',
            'Connection' => 'keep-alive',
            'Referer' => 'https://online.mkb.ru' . $this->stepTwoUrl,
            'TE' => 'Trailers'
        ]);
        $response = $this->request($request);
        return $response;
    }

    private function getBalance($data)
    {
        $json = json_decode($data);
        if (!isset($json->d[0])) {
            throw new \Exception('В ответе нет баланса');
        }
        $jsonData = $json->d[0];
        $balance = floatval(str_replace(' ', '', str_replace(',', '.', $jsonData->OstEnd)));
        return $balance;
    }

}