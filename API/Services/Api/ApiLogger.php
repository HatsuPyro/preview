<?php

namespace App\Services\Api;

use App\Models\ApiLog;
use App\Models\ApiMethod;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiLogger
{
    const API_LOG_NAME = 'name';
    const API_LOG_USER = 'user';
    const API_LOG_IP = 'ip';
    const API_LOG_DATE = 'date';
    const API_USER_LINK = 'link';

    /**
     * Сохраняет оередной апи лог
     * @param $code
     */
    public static function setApiLog($code)
    {
        $apiLog = new ApiLog();
        $apiMethod = ApiMethod::where('code', $code)->first();
        $apiLog->addApiLog($apiMethod->id, Auth::user()->id, Request::capture()->ip());
    }

    /**
     * Возвращает апи лог
     * @return array
     */
    public static function getApiLog()
    {
        $result = array_filter(ApiLog::orderBy('updated_at', 'desc')->take(9)->get()
            ->map(function ($apiLog) {
                $apiMethod = ApiMethod::find($apiLog->api_method_id);
                $user = User::find($apiLog->user_id);
                if (!$apiMethod) {
                    return null;
                }

                if (!$user) {
                    return null;
                }
                return [
                    self::API_LOG_NAME => $apiMethod->name,
                    self::API_LOG_USER => $user->name,
                    self::API_LOG_IP => $apiLog->user_ip,
                    self::API_LOG_DATE => $apiLog->updated_at,
                    self::API_USER_LINK => route('users', ['key' => $user->id])
                ];
            })->toArray());
        return $result;
    }
}