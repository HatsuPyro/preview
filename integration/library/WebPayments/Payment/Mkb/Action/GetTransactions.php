<?php


namespace Pyrobyte\WebPayments\Payment\Mkb\Action;

use App\Extensions\Payment\TransactionTranslator;
use Pyrobyte\WebPayments\Request;

class GetTransactions extends \Pyrobyte\WebPayments\Payment\ActionAbstract
{
    const DATE_FORMAT = 'Y-m-d\TH:i:s';
    protected $transactions = [];
    public $resultClass = '\Pyrobyte\WebPayments\Payment\Mkb\Result\GetTransactions';
    public $fromDate = null;
    public $toDate = null;

    public function __construct($fromDate = null, $toDate = null)
    {
        if ($fromDate) {
            $this->fromDate = new \DateTime($fromDate);
        }
        if ($toDate) {
            $this->toDate = new \DateTime($toDate);
        }
    }

    public function run()
    {
        $this->stepOne();
        $this->stepTwo();
        $response = $this->stepThree();
        $this->parseTransactions($response->getContent());

        $result = new $this->resultClass($response);
        $result->transactions = $this->transactions;
        return $result;
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
            'https://online.mkb.ru/secure/CardOperations.aspx?=&df=' . $this->fromDate->format('d.m.Y') . '&dt=' . $this->toDate->format('d.m.Y') . '&updateHistory=true',
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
        $response = $this->request($request);
        return $response;
    }

    private function parseTransactions($html)
    {
        preg_match_all("/Дата транзакции:<\/td\><td\>(.+)<br\/>/U", $html, $matches);
        $dateTrans = $matches[1];
        preg_match_all("/Дата проводки:<\/td\><td\>(.+)<\/td>/U", $html, $matches);
        $dateProvodki = $matches[1];
        preg_match_all("/Дата проводки:<\/td\><td\>.+<\/td>.+<div.+\>(.+)<\/div>/U", $html, $matches);
        $description = $matches[1];
        preg_match_all("/Сумма в валюте счета:<\/td\><td\>(.+)<\/td>/U", $html, $matches);
        $amount = $matches[1];

        $transaction = [];

        for ($i = 0; $i < count($dateTrans); $i++) {
            preg_match('/(.+);/', $amount[$i], $matches);

            if (!isset($matches[1])) {
                throw new \Exception('В одной из транзакций нет суммы');
            }

            $transaction[TransactionTranslator::FIELD_AMOUNT] = str_replace([' ', ' ', '&nbsp', ','], ['', '', '', '.'], trim($matches[1]));
            $transaction[TransactionTranslator::FIELD_DATE] = $dateTrans[$i];
            $transaction[TransactionTranslator::FIELD_DESCRIPTION] = $description[$i];
            preg_match('/.+;(.+)($|\.)/', $amount[$i], $matches);
            $transaction[TransactionTranslator::FIELD_CURRENCY] = $matches[1];
            $transaction['date_provodki'] = $dateProvodki[$i];
            $this->transactions[] = $transaction;
        }
    }

}