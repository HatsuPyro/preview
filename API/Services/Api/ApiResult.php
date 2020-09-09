<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 03.10.2018
 * Time: 14:45
 */

namespace App\Services\Api;

/**
 * Класс результата выполнения api метода
 * Class ApiResult
 * @package App\Services\Api
 */
class ApiResult
{
    const STATUS_SUCCESS = 'success';

    private $attributes = [];
    private $apiCode = '';

    public function __construct($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * Получает ответ для апи контроллера
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getApiResponse()
    {
        return response()->json($this->attributes);
    }

    /**
     * @return string
     */
    public function getApiCode()
    {
        return $this->apiCode;
    }

    /**
     * @param $code
     */
    public function setApiCode($code)
    {
        $this->apiCode = $code;
    }
}
