<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 20.09.2018
 * Time: 17:13
 */

namespace App\Extensions\Payment;


interface ValidatorInterface
{

    /**
     * Создатель валидатора.
     * @param $validationData
     * @param $rules
     * @return Validator
     */
    public static function make($validationData, $rules);

    /**
     * Проверка провала валидации
     * @return bool
     */
    public function fails();

    /**
     * Получает сообщения об ошибках
     * @return array
     */
    public function getMessages();

}