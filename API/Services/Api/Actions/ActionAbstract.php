<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 03.10.2018
 * Time: 15:15
 */

namespace App\Services\Api\Actions;

use App\Exceptions\AccessDeniedException;
use App\Models\Permission;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class ActionAbstract implements ActionInterface
{
    use ValidatesRequests;

    protected $resource;
    protected $permission = Permission::ACTION_READ;

    /**
     * Функция инициализации
     */
    public function init()
    {
        $resource = $this->resource;
        if($resource) {
            if(!is_array($this->resource)) {
                $resource = [$resource];
            }
            foreach ($resource as $res) {
                if(can($res, $this->permission)) {
                    return;
                }
            }
            throw new AccessDeniedException('У вас нет доступа к этой операции');
        }
    }

    /**
     * Получает параметр из тела запроса
     * @param $code
     * @return mixed|null
     */
    protected function getBodyParam($code)
    {
        $param = request()->json()->get($code) ?? null;

        return $param;
    }
}
