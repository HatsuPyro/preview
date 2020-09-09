<?php

namespace App\Services\Api\Actions;

use App\Entities\Rbac\Resource;
use App\Models\ApiMethod;
use App\Models\Permission;
use App\Services\UpdateServices\Wallets\ApiRowUpdateService;
use App\Services\Api\ApiResult;

/**
 * Api-метод обновления информации о кошельке с помощью передаваемых параметров
 * Class WalletUpdate
 * @package App\Services\Api\Actions
 */
class WalletUpdate extends WalletActionAbstract
{
    private $data = null;
    protected $showInvalidParameters = true;
    protected $resource = Resource::WALLETS;
    protected $permission = Permission::ACTION_UPDATE;

    public function __construct($walletNumber, $payment, $data)
    {
        parent::__construct($walletNumber, $payment);
        $this->data = $data;
    }

    public function do()
    {
        if (empty($this->data)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'Все параметры' => 'Параметры запроса не были обработаны',
            ]);
        }
        $updateService = new ApiRowUpdateService($this->wallet, $this->data);
        try {
            $result = $updateService->update();
            $message = "Кошелёк успешно обновлён!";
        } catch (\Exception $e) {
            throw $e;
        }

        $apiResult = new ApiResult([
            "result" => [
                "status" => !empty($result),
                "message" => $message
            ]
        ]);
        $apiResult->setApiCode(ApiMethod::CODE_WALLET_EDIT);
        return $apiResult;
    }
}
