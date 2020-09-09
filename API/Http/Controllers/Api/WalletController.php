<?php

/**
 * Контроллер действий над кошельком
 */

namespace App\Http\Controllers\Api;

use App\Services\Api\Actions\CheckTask;
use App\Services\Api\Actions\GetClientWallets;
use App\Services\Api\Actions\GetWalletBalance;
use App\Services\Api\Actions\GetWalletHistory;
use App\Services\Api\Actions\GetWalletInfo;
use App\Services\Api\Actions\GetWalletsToPay;
use App\Services\Api\Actions\Payout;
use App\Services\Api\Actions\Reserve;
use App\Services\Api\Actions\Unreserve;
use App\Services\Api\Actions\UpdateBalance;
use App\Services\Api\Actions\UpdateSimBalance;
use App\Services\Api\Actions\UpdateTransactions;
use App\Services\Api\Actions\WalletUpdate;
use App\Services\Api\Executor;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Возвращает сохраненный баланс кошелька
     * @param Request $request
     * @param $payment
     * @param $walletNumber
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function balance(Request $request, $payment, $walletNumber)
    {
        $executor = new Executor(new GetWalletBalance($walletNumber, $payment));
        return $executor->execute();
    }

    /**
     * Обновляет баланс симки
     * @param Request $request
     * @param $payment
     * @param $walletNumber
     * @return Executor
     */
    public function updateSimBalance(Request $request, $payment, $walletNumber)
    {
        return $this->executeAction(new UpdateSimBalance($walletNumber, $payment));
    }

    private function executeAction($action)
    {
        $executor = new Executor($action);
        return $executor->execute();
    }

    /**
     * Получает историю транзакций кошелька
     * @param Request $request
     * @param $payment
     * @param $walletNumber
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function history(Request $request, $payment, $walletNumber)
    {
        $executor = new Executor(new GetWalletHistory($walletNumber, $payment));
        return $executor->execute();
    }

    /**
     * Обновляет историю операций у заданного кошелька
     * @param Request $request
     * @param $payment
     * @param $walletNumber
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function updateHistory(Request $request, $payment, $walletNumber)
    {
        $executor = new Executor(new UpdateTransactions($walletNumber, $payment));
        return $executor->execute();
    }

    /**
     * @param Request $request
     * @param         $payment
     * @param         $walletNumber
     *
     * @return mixed
     */
    public function updateBalanceSms(Request $request, $payment, $walletNumber)
    {
        $executor = new Executor(new UpdateTransactions($walletNumber, $payment));
        return $executor->execute();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function walletsToPay(Request $request)
    {
        $executor = new Executor(new GetWalletsToPay($request->get('payment'), false));
        return $executor->execute();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function paymentWalletsToPay(Request $request, $payment)
    {
        $executor = new Executor(new GetWalletsToPay($payment));
        return $executor->execute();
    }

    /**
     * Получает список кошельков пользователя
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function wallets(Request $request)
    {
        $executor = new Executor(new GetClientWallets($request->get('payment'), false));
        return $executor->execute();
    }

    /**
     * Получает список кошельков пользователя
     * @param Request $request
     * @param $payment
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function paymentWallets(Request $request, $payment)
    {
        $executor = new Executor(new GetClientWallets($payment));
        return $executor->execute();
    }

    /**
     * Получает информацию о кошельке
     * @param Request $request
     * @param $payment
     * @param $walletNumber
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function info(Request $request, $payment, $walletNumber)
    {
        $executor = new Executor(new GetWalletInfo($walletNumber, $payment));
        return $executor->execute();
    }

    /**
     * Производит автовывод с кошелька
     * @param Request $request
     * @param $payment
     * @param $walletNumber
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function payout(Request $request, $payment, $walletNumber)
    {
        $executor = new Executor(new Payout($walletNumber, $payment));
        return $executor->execute();
    }

    /**
     * Обновляет баланс кошелька
     * @param Request $request
     * @param $payment
     * @param $walletNumber
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function updateBalance(Request $request, $payment, $walletNumber)
    {
        $executor = new Executor(new UpdateBalance($walletNumber, $payment));
        return $executor->execute();
    }

    /**
     * Проверяет выполнение задачи операции над кошельком
     * @param Request $request
     * @param $task
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\Api\Exceptions\AccessDeniedException
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function taskStatus(Request $request, $task)
    {
        $executor = new Executor(new CheckTask($task));
        return $executor->execute();
    }

    /**
     * Изменяет поля кошелька на переданные параметры(редактирование кошелька)
     * @param Request $request
     * @param $payment
     * @param $walletNumber
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Services\WalletOperations\Exceptions\OperationsServiceException
     */
    public function walletEdit(Request $request, $payment, $walletNumber)
    {
        $executor = new Executor(new WalletUpdate($walletNumber, $payment, $request->json()->all()));
        return $executor->execute();
    }

    /**
     * Резервирует кошелек
     * @param Request $request
     * @param $payment
     * @param $walletNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function reserve(Request $request, $payment, $walletNumber)
    {
        return $this->executeAction(new Reserve($walletNumber, $payment));
    }

    /**
     * Разрезервирует кошелек
     * @param Request $request
     * @param $payment
     * @param $walletNumber
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreserve(Request $request, $payment, $walletNumber)
    {
        return $this->executeAction(new Unreserve($walletNumber, $payment));
    }
}
