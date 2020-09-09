<?php

namespace Pyrobyte\WebPayments\Traits;

trait Loginable
{
    protected $account;
    protected $password;

    /**
     * Получает телефон бинбанк в международном формате без "+"
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Задает аккаунт
     *
     * @param $account
     * @return $this
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * Получает пароль аккаунта бинбанк
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Задает пароль
     *
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
}