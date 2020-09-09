<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.09.2018
 * Time: 14:43
 */

namespace Pyrobyte\Sesame\Result;


use Pyrobyte\Sesame\Exceptions\NoResultException;
use Pyrobyte\Sesame\Result\Entities\Phone;
use Pyrobyte\Sesame\ResultAbstract;

class SimList extends ResultAbstract
{
    protected $numbers = [];

    public function __construct($response)
    {
        parent::__construct($response);

        if(!isset($this->response->data)) {
            throw new NoResultException('При получении сим-карт не был получен результат операции');
        }

        $dataNumbers = $this->response->data->list;

        foreach ($dataNumbers as $dataNumber) {
            $this->numbers[] = new Phone($dataNumber);
        }
    }

    /**
     * Получает массив номеров
     * @return array
     */
    public function getNumbers()
    {
        return $this->numbers;
    }

    protected function setValidationRules()
    {
        $rules = [
            'data.list.*.id' => 'regex:/\w+/',
            'data.list.*.number' => 'string|nullable',
        ];

        $this->rules = $rules;
    }
}