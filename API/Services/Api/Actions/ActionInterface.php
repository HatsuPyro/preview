<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 03.10.2018
 * Time: 14:39
 */

namespace App\Services\Api\Actions;

use App\Services\Api\ApiResult;

interface ActionInterface
{
    /**
     * Производит действие и возвращает результат выполнения
     * @return ApiResult
     */
    public function do();
}
