<?php
namespace App\Services\Api\Exceptions;

/**
 * Исключение попытки активации кошелька, который был отключен из-за превышения лимита
 * Class TryActivateDisabledReachingLimitsWalletException
 * @package App\Services\Api\Exceptions
 */
class TryActivateDisabledReachingLimitsWalletException extends \Exception
{

}