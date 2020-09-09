<?php
namespace App\Extensions\Payment\Exception;

/**
 * Класс-исключение для невалидных ответов ПС. Кошелек отключаем только при нескольких ошибок подряд
 * Class InvalidResponseException
 * @package App\Extensions\Payment\Exception
 */
class InvalidResponseException extends AuthenticationException
{

}