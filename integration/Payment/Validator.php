<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 20.09.2018
 * Time: 16:04
 */

namespace App\Extensions\Payment;

class Validator implements ValidatorInterface
{
    private $data = [];
    private $rules = [];
    private $messages = [];

    public function __construct($validationData, $rules)
    {
        $this->data = $validationData;
        $this->rules = $rules;
    }

    /**
     * Создатель валидатора. Написан, т.к. конструктор может измениться
     * @param $validationData
     * @param $rules
     * @return Validator
     */
    public static function make($validationData, $rules)
    {
        return new self($validationData, $rules);
    }

    /**
     * Проверка провала валидации
     * @return bool
     */
    public function fails()
    {
        foreach ($this->rules as $key => $rule) {

            $dataItem = $this->data[$key] ?? null;

            $subRules = explode('|', $rule);

            // TODO: вынести в отдельные классы-валидаторы
            foreach ($subRules as $subRule) {
                $rule = $this->getRuleFromString($subRule);
                switch ($rule) {
                    case 'required':
                        if(empty($dataItem)) {
                            $this->messages[$key] = 'Поле ' . $key . ' обязательно.';
                            return true;
                        }
                        break;
                    case 'date_format':
                        $format = mb_substr($subRule, mb_strpos($subRule, ':') + 1);
                        try {
                            $date = \DateTime::createFromFormat($format, $dataItem);
                        } catch (\Exception $e) {
                            $this->messages[$key] = 'Поле ' . $key . ' должно быть датой формата: ' . $format . '.';
                            return true;
                        }
                        break;
                }
            }
        }
        return false;

    }

    private function getRuleFromString($rule)
    {

        return explode(':', $rule)[0];
    }

    /**
     * Получает сообщения об ошибках
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}