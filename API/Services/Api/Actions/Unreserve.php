<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 5/21/19
 * Time: 3:51 PM
 */

namespace App\Services\Api\Actions;

use App\Entities\Rbac\Resource;
use App\Models\ApiMethod;
use App\Models\Permission;
use App\Services\Api\ApiResult;
use Illuminate\Validation\ValidationException;

class Unreserve extends WalletActionAbstract
{
    protected $resource = Resource::RESERVE;
    protected $permission = Permission::ACTION_UPDATE;

    public function do()
    {
        $wallet = $this->wallet;
        $status = $wallet->getStatus();
        if(!$status->isReserved()) {
            throw new ValidationException(null);
        }
        $status->unreserve();
        $status->save();

        $apiResult = new ApiResult([
            'status' => 'success',
        ]);
        $apiResult->setApiCode(ApiMethod::CODE_WALLET_UNRESERVE);
        return $apiResult;
    }
}
