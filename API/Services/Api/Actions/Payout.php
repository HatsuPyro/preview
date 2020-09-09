<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 29.10.2018
 * Time: 15:34
 */

namespace App\Services\Api\Actions;

use App\Entities\Rbac\Resource;
use App\Models\ApiMethod;
use App\Models\Permission;
use App\Models\TaskLog;
use App\Services\Api\ApiResult;
use App\Services\Api\Exceptions\OperationTimeoutException;
use App\Services\Tasks\Checker;
use App\Services\WalletOperations\ExecutionData\InitPayoutData;
use App\Services\WalletOperations\Executor;
use App\Services\WalletOperations\Operations\InitPayout as PayoutOperation;

/**
 * Api-метод проведения автовывода с кошелька
 * Class InitPayoutOld
 * @package App\Services\Api\Actions
 */
class Payout extends WalletActionAbstract
{
    protected $resource = [Resource::PAYOUT, Resource::PAYOUT_BY_REQUISITES];
    protected $permission = Permission::ACTION_UPDATE;

    public function do()
    {
        $wallet = &$this->wallet;
        $sum = $this->getBodyParam('sum');
        // Если сумма получена из запроса - зададим ей тип, чтобы в дальнейшем можно было проверять на null
        $checkBalance = $this->getBodyParam('checkBalance') ?? false;
        $reserve = $this->getBodyParam('reserve') ?? false;

        if ($sum !== null) {
            $sum = (float)$sum;
        }
        if ($checkBalance !== null) {
            $checkBalance = (bool)$checkBalance;
        }
        $data = new InitPayoutData(
            $wallet,
            $this->getBodyParam('method'),
            $this->getBodyParam('account'),
            $sum,
            $checkBalance,
            false,
            $reserve
        );

        $operationExecutor = new Executor(new PayoutOperation($data), CHANNEL_API);
        $operationExecutor->execute();

        $log = $operationExecutor->getLog();

        $apiResult = new ApiResult([
            'task' => $log->id,
        ]);
        $apiResult->setApiCode(ApiMethod::CODE_WALLET_PAYOUT);
        return $apiResult;
    }
}
