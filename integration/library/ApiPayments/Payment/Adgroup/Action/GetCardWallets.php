<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/28/19
 * Time: 12:56 PM
 */

namespace Pyrobyte\ApiPayments\Payment\Adgroup\Action;


class GetCardWallets extends GetWallets
{
    /**
     * @inheritdoc
     */
    protected function filter($wallet)
    {
        if($wallet->protocol_type == self::PROTOCOL_CARD) {
            return true;
        }
        return false;
    }
}