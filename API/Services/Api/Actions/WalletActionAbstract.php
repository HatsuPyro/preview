<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 03.10.2018
 * Time: 15:45
 */

namespace App\Services\Api\Actions;

use App\Models\Currency;
use App\Models\MetaPayment;
use App\Models\Wallet;
use App\Services\Api\Exceptions\AccessDeniedException;
use App\Services\Api\Exceptions\WalletNotFoundExcepttion;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class WalletActionAbstract extends ActionAbstract
{
    protected $walletNumber = null;
    protected $payment = null;
    /**
     * @var Wallet
     */
    protected $wallet = null;
    protected $showInvalidParameters = false;
    protected $currency = null;

    public function __construct($walletNumber, $payment)
    {
        $this->payment = $payment;
        $this->walletNumber = $walletNumber;
        $this->currency = request('currency',Currency::CODE_RUR);
    }

    public function init()
    {
        parent::init();
        $user = \Auth::user();
        $this->wallet = $this->getWallet($this->walletNumber);

        if (!$user->hasWallet($this->wallet)) {
            throw new AccessDeniedException('Вы не являетесь владельцем этого кошелька');
        }
    }

    /**
     * Получает запись кошелька из бд
     *
     * @param $walletNumber
     *
     * @return mixed
     * @throws WalletNotFoundExcepttion
     */
    protected function getWallet($walletNumber)
    {
        try {
            $returnQuery = Wallet::where(function ($query) use ($walletNumber) {
                $query->whereNumber($walletNumber)
                    ->orWhere(function ($query) use ($walletNumber) {
                        $query->where('card_number', '!=', '')
                            ->where('card_number', $walletNumber);
                    });
            })->whereCurrency($this->currency);
            if (in_array($this->payment, MetaPayment::CODES)) {
                $codePayments = MetaPayment::getPaymentCode($this->payment);
                $returnQuery = $returnQuery->whereIn('payment', $codePayments);
            } else {
                $returnQuery = $returnQuery->where('payment', $this->payment);
            }
            return $returnQuery->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new WalletNotFoundExcepttion('Кошелек платежной системы ' . $this->payment
                . ' с номером ' . $walletNumber . ' валюты ' . $this->currency . ' не найден');
        }
    }

    /**
     * Надо ли показывать невалидные параматреы запроса на ошибку валидации
     * @return bool
     */
    public function needShowInvalidParameters()
    {
        return $this->showInvalidParameters;
    }
}
