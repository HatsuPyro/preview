<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.09.2018
 * Time: 10:38
 */

namespace Pyrobyte\Sesame;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Основной класс для работы с SimBox
 * Class Client
 * @package Pyrobyte\Sesame
 */
class Client
{
    private $httpClient = null;
    private $options = ['headers' => []];

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->httpClient = new GuzzleClient([
            'base_uri' => Config::getItem('base_uri'),
        ]);
        $this->options['connect_timeout'] = 15;
        $options = &$this->options;
        $headers = &$options['headers'];
        $headers['API_KEY'] = Config::getItem('api_key');
        $headers['Cache-Control'] = 'no-cache';
        $headers['Content-Type'] = 'application/json';
    }

    /**
     * Выполняет запрос к sim.yobot.kiwi
     * @param ActionInterface $action
     * @return mixed
     */
    public function call(ActionInterface $action)
    {
        $options = $this->options;
        $result = $action->call($this->httpClient, $options);

        return $result;
    }
}