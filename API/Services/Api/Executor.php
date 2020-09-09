<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 03.10.2018
 * Time: 14:34
 */

namespace App\Services\Api;

use App\Services\Api\Actions\ActionInterface;
use App\Services\Api\Exceptions\AccessDeniedException;
use App\Services\Api\Exceptions\OperationTimeoutException;
use App\Services\Api\Exceptions\TryActivateBlockedWalletException;
use App\Services\Api\Exceptions\TryActivateDisabledReachingLimitsWalletException;
use App\Services\Api\Exceptions\WalletNotFoundExcepttion;
use App\Services\Tasks\Exceptions\TaskCheckException;
use App\Services\Tasks\Exceptions\TaskExecutionDisabledException;
use App\Services\Wallet\CanServices\Exceptions\PaymentBlockedException;
use App\Services\WalletOperations\Exceptions\OperationsServiceException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * Выполняет действия для апи
 * Class Executor
 * @package App\Services\Api
 */
class Executor
{
    private $action = null;

    public function __construct(ActionInterface $action)
    {
        $this->action = $action;
    }

    public function execute()
    {
        try {
            $this->action->init();
            $result = $this->action->do();
            ApiLogger::setApiLog($result->getApiCode());
            return $result->getApiResponse();
        } catch (TaskExecutionDisabledException $e) {
            throw new OperationsServiceException($e->getMessage(), ERROR_CODE_TASK_EXECUTION_OFF);
        } catch (ValidationException $e) {

            if ($this->action->needShowInvalidParameters()) {
                $errorCode = ERROR_CODE_INVALID_REQUEST_PARAMETERS;
                $errorMessage = 'Следующие параметры не валидны: ' . validation_exception_to_pretty($e);
            } else {
                $errorCode = ERROR_CODE_WRONG_REQUEST;
                $errorMessage = 'Ошибка в параметрах запроса';
            }
            return response()->json([
                'errorCode' => $errorCode,
                'errorMessage' => $errorMessage,
            ], 400);
        } catch (PaymentBlockedException $e) {
            throw new OperationsServiceException($e->getMessage(), ERROR_CODE_PAYMENT_BLOCKED);
        } catch (OperationsServiceException $e) {
            return $this->getErrorResponse($e->getCode(), $e->getMessage());
        } catch (TaskCheckException $e) {
            return $this->getErrorResponse($e->getCode(), $e->getMessage());
        } catch (OperationTimeoutException $e) {
            return $this->getErrorResponse(ERROR_CODE_OPERATION_TIMEOUT, 'Ошибка таймаута выполнения задачи');
        } catch (TryActivateBlockedWalletException $e) {
            return $this->getErrorResponse(ERROR_CODE_TRY_ACTIVATE_BLOCKED_WALLET, $e->getMessage());
        } catch (TryActivateDisabledReachingLimitsWalletException $e) {
            return $this->getErrorResponse(ERROR_CODE_TRY_ACTIVATE_DISABLE_REACHING_LIMITS_WALLET, $e->getMessage());
        } catch (AccessDeniedException $e) {
            return $this->getErrorResponse(ERROR_CODE_ACCESS_DENIED, $e->getMessage());
        } catch (WalletNotFoundExcepttion $e) {
            return $this->getErrorResponse(ERROR_CODE_WALLET_NOT_FOUND, $e->getMessage());
        } catch (\App\Exceptions\AccessDeniedException $e) {
            abort(403, $e->getMessage());
        } catch (\Exception $e) {
            Log::channel('api')->error($e);
            return response()->json([
                'errorCode' => ERROR_CODE_TECHNICAL,
                'errorMessage' => 'Техническая ошибка, нельзя отправить запрос провайдеру',
            ], 500);
        }
    }

    public function getErrorResponse($code, $message, $httpCode = 500)
    {
        return response()->json([
            'errorCode' => $code,
            'errorMessage' => $message,
        ], $httpCode);
    }
}
