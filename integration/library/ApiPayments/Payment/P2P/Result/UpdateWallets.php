<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 20.11.19
 * Time: 12:17
 */

namespace Pyrobyte\ApiPayments\Payment\P2P\Result;

use Pyrobyte\ApiPayments\Result\ResultAbstract;

class UpdateWallets extends ResultAbstract
{
    private $updateInfo = [];

    public function __construct($updateInfo)
    {
        $this->updateInfo = $updateInfo;
    }

    /**
     * get updateInfo
     * @return array
     */
    public function getTimeUpdate()
    {
        return $this->updateInfo->time_update;
    }

}