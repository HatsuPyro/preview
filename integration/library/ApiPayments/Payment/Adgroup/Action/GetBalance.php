<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/18/19
 * Time: 2:30 PM
 */

namespace Pyrobyte\ApiPayments\Payment\Adgroup\Action;

use Pyrobyte\ApiPayments\Payment\Adgroup\Result\GetBalance as Result;
use Illuminate\Support\Facades\Cache;

class GetBalance extends ActionAbstract
{
    private $walletNumber = null;
    protected $url = '/merchant/get-wallet-list';
    protected $txName = 'fetchWallets';

    public function __construct($walletNumber, $provider)
    {
        $this->walletNumber = $walletNumber;
        parent::__construct($provider);
    }

    /**
     * @inheritdoc
     */
    protected function makeResult($result)
    {
        $walletsArr = $result;
        foreach ($walletsArr as $wallet) {
            if($wallet->tel == $this->walletNumber) {
                return new Result((float)$wallet->rub);
            }
        }
        throw new \Exception('Информация по данному кошельку не была получена при запросе в адгруп');
    }
}