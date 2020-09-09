<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.09.2018
 * Time: 11:04
 */

namespace Pyrobyte\Sesame;


interface ActionInterface
{
    /**
     * Получает урл
     * @return mixed
     */
    public function getUrl();

    /**
     * Получает метод
     * @return mixed
     */
    public function getMethod();

    /**
     * Получает заголовки
     * @return mixed
     */
    public function getHeaders();

    /**
     * Производит запрос
     * @param $httpClient
     * @param $options
     * @return mixed
     */
    public function call($httpClient, $options);

    /**
     * Получает результат запроса
     * @return mixed
     */
    public function getResult();

}