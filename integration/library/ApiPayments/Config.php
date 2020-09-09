<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 3/15/19
 * Time: 3:34 PM
 */

namespace Pyrobyte\ApiPayments;


class Config
{
    private static $config = [
        'client_id' => null,
        'client_secret' => null,
        'adgroup' => [
            'mass_update' => [
                'total_count' => 2000,
            ]
        ]
    ];

    public static function setConfig($config)
    {
        foreach ($config as $key => $configItem) {
            self::setConfigItem($configItem, $key);
        }
    }

    private static function setConfigItem($configItem, $key)
    {
        if (is_array($configItem)) {
            foreach ($configItem as $subKey => $configSubItem) {
                self::setConfigItem($configSubItem, $subKey);
            }
        } else {
            self::$config[$key] = $configItem;
        }
    }

    public static function getConfig()
    {
        return self::$config;
    }

    public static function getItem($key)
    {
        $explodedKey = explode('.', $key);
        $value = self::$config;
        $currentKey = array_shift($explodedKey);

        do {
            if (empty($value[$currentKey])) {
                throw new \Exception(self::getEmptyKeyError($currentKey));
            }
            $value = $value[$currentKey];
            $currentKey = array_shift($explodedKey);
        } while (!empty($currentKey));

        return $value;
    }

    /**
     * Get error if value of key is empty
     * @param $key
     * @return mixed|string
     */
    private static function getEmptyKeyError($key)
    {
        $errorsMap = [
            'client_id' => 'Не задан client id для Adgroup',
            'client_secret' => 'Не задан client secret  для Adgroup',
        ];
        return $errorsMap[$key] ?? 'Wrong config key';
    }
}