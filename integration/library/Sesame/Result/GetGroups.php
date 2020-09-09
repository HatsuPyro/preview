<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.11.2018
 * Time: 15:08
 */

namespace Pyrobyte\Sesame\Result;


use Pyrobyte\Sesame\Exceptions\NoResultException;
use Pyrobyte\Sesame\Result\Entities\Group;
use Pyrobyte\Sesame\ResultAbstract;

class GetGroups extends ResultAbstract
{
    private $groups = [];

    public function __construct($response)
    {
        parent::__construct($response);

        if(!isset($this->response->data)) {
            throw new NoResultException('При получении групп сим-карт не был полуен результат операции');
        }

        $groups = [];
        foreach ($this->response->data as $dataGroup) {
            $groups[] = new Group($dataGroup->id, $dataGroup->name);
        }

        $this->groups = $groups;
    }

    /**
     * Получает массив групп
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }


}