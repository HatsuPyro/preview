<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 20.11.2018
 * Time: 16:31
 */

namespace App\Extensions\Payment\Exception;

/**
 * Класс-исключение превышения времени ожидания смс
 * Class GetSmsTimeoutException
 * @package App\Extensions\Payment\Exception
 */
class GetSmsTimeoutException extends SmsServiceException
{

}