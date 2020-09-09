<?php

namespace App\Extensions\Payment\Exception;

/**
 * Класс-исключение для случаев, когда необходимо отключить кошелек
 * Class CriticalException
 * @package App\Extensions\Payment\Exception
 */
class CriticalException extends AuthenticationException
{

}