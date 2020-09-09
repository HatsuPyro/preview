<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 19.11.2018
 * Time: 15:10
 */

namespace Pyrobyte\Sesame\Result\Entities;


class Group
{
    private $id = null;
    private $name = null;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }
}