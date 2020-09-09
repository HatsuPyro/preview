<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.09.2018
 * Time: 16:13
 */

namespace Pyrobyte\Sesame\Result;


use Pyrobyte\Sesame\Exceptions\NoResultException;
use Pyrobyte\Sesame\Result\Entities\Message;
use Pyrobyte\Sesame\ResultAbstract;

class GetMessages extends ResultAbstract
{
    protected $messages = [];
    public function __construct($response)
    {
        parent::__construct($response);
        if(!isset($this->response->data)) {
            throw new NoResultException('При получении сообщений не был получен результат операции');
        }
        $this->messages = $this->response->data;
    }

    public function getMessages()
    {
        $rawMessages = $this->messages;

        $messages = array_map(function($item) {
            return new Message($item->text, $item->time, $item->from, $item->id);
        }, $rawMessages);
        return $messages;
    }

    protected function setValidationRules()
    {
        $rules = [
            'data.*.id' => 'regex:/\w+/',
            'data.*.time' => 'numeric',
            'data.*.from' => 'string',
            'data.*.text' => 'string',
            'data.*.to' => 'string',
        ];

        $this->rules = $rules;
    }
}