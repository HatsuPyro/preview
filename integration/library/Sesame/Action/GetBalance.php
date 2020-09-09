<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 14.11.2018
 * Time: 13:51
 */

namespace Pyrobyte\Sesame\Action;


use App\Models\Payment;
use App\Models\PaymentSettings;
use App\Models\SettingsItem;
use Pyrobyte\Sesame\ActionAbstract;
use Pyrobyte\Sesame\Config;
use Pyrobyte\Sesame\Entities\Operator;
use Pyrobyte\Sesame\Exceptions\UnknownOperatorException;

class GetBalance extends ActionAbstract
{
    const TYPE_USSD = 'ussd',
        TYPE_SESAME = 'sesame';

    protected $url = 'send_ussd';
    protected $resultClass = \Pyrobyte\Sesame\Result\GetBalance::class;
    protected $method = self::METHOD_POST;

    public function __construct(string $simId, string $operator = null)
    {
        $type = Config::getItem('get_balance_type');
        $this->body = ['from' => $simId];
        if($type == self::TYPE_SESAME) {
            $this->url = 'get_balance';
            return;
        }
        $ussd = null;
        $payment = Payment::getByCode(Payment::CODE_TELE2);
        $ussdMap = [
            Operator::CODE_TELE2 => $payment->getSettings(Payment::SERVICE_SESAME)->getCommandBalance(),
            Operator::CODE_BEELINE => '*102#',
            Operator::CODE_MTS => '*100#',
            Operator::CODE_MEGAFON => '*100#',
        ];
        $ussd = $ussdMap[$operator] ?? null;
        if($ussd === null) {
            throw new UnknownOperatorException('Операция получения баланса для оператора "'
                . ($operator ?? 'null') . '" не определена.');
        }
        $this->body['ussd'] = $ussd;
    }
}