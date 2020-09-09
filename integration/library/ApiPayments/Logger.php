<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 4/30/19
 * Time: 11:19 AM
 */

namespace Pyrobyte\ApiPayments;


use Pyrobyte\ApiPayments\Engine\Request;
use Pyrobyte\ApiPayments\Engine\Response;

class Logger extends \Pyrobyte\Logger\Logger
{
    const LEVEL_REQUESTS = 'requests';
    const LEVEL_RESPONSES = 'responses';

    /**
     * Логирует отправленный запррс
     * @param $request
     * @return $this
     */
    public function logSentRequest(Request $request)
    {
        $this->addMessage('Запрос отправлен: ' . $request->getUrl(), self::LEVEL_REQUESTS);
        return $this;
    }

    public function logResponse(Response $response)
    {
        $this->addMessage(
            $response,
            self::LEVEL_RESPONSES
        );
        return $this;
    }
}