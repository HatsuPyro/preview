<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/5/19
 * Time: 10:40 AM
 */

namespace Pyrobyte\ApiPayments\Payment\Adgroup\Action\TransactionFilter;


class FilterProtocol implements FilterInterface
{
    const WALLET = ['TRANSFER', 'BILL'];
    const CARD = 'CARD';
    const YANDEX = 'YANDEX';
    private $protocols = null;

    public function __construct(array $protocols)
    {
        $this->protocols = (getType($protocols[0])  == 'array') ? $protocols[0] : $protocols;
    }

    public function get()
    {
        return ['protocol_type' => $this->protocols];
    }
}