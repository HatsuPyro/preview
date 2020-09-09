<?php

namespace App\Services\Api;

use App\Models\User;

/**
 * Сервис создания токенов
 * Class CreateToken
 * @package App\Services\Api
 */
class CreateToken
{
	public function create(User $user, array $scopes = [], $name = null)
	{
	    if($name == null) {
	        $name = implode(' ', $scopes) . ' ' . config('app.name');
        }
        $token = $user->createToken($name, $scopes)->accessToken;

        return $token;
	}
}