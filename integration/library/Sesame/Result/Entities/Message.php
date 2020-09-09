<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 13.09.2018
 * Time: 18:13
 */

namespace Pyrobyte\Sesame\Result\Entities;


class Message
{
    public $message = null;
    public $date = null;
    public $from = null;
    public $id = null;

    const TELE2_CODE = 'Tele2';


    public function __construct($message, $date, $from, $id)
    {
        $this->message = $message;
        $this->date = $date;
        $this->from = $from;
        $this->id = $id;
    }
}