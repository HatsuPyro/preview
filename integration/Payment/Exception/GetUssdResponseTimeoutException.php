<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 27.11.2018
 * Time: 18:22
 */

namespace App\Extensions\Payment\Exception;

/**
 * Класс-исключение превышения времени ожидания ответа от ussd запроса
 * Class GetUssdResponseTimeoutException
 * @package App\Extensions\Payment\Exception
 */
class GetUssdResponseTimeoutException extends PaymentException
{

}