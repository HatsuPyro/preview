<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 5/21/19
 * Time: 3:42 PM
 */

namespace App\Services\Api\Actions;

use App\Entities\Rbac\Resource;
use App\Models\ApiMethod;
use App\Models\Permission;
use App\Models\WalletStatus;
use App\Services\Api\ApiResult;
use Illuminate\Validation\ValidationException;

class Reserve extends WalletActionAbstract
{
    protected $resource = Resource::RESERVE;
    protected $permission = Permission::ACTION_UPDATE;
    
    public function do()
    {
        $wallet = $this->wallet;
        $status = $wallet->getStatus();
        if($status->isReserved() && $status->getReservedFrom() !== WalletStatus::RESERVE_FROM_API) {
            throw new ValidationException(null);
        }
        $status->reserve(WalletStatus::RESERVE_FROM_API);
        $status->save();

        $apiResult = new ApiResult([
            'status' => 'success',
        ]);
        $apiResult->setApiCode(ApiMethod::CODE_WALLET_RESERVE);
        return $apiResult;
    }
}
