<?php
/**
 * Created by nikita@hotbrains.ru
 * Date: 12.09.2018
 * Time: 14:44
 */

namespace Pyrobyte\Sesame;


use Illuminate\Support\Facades\Validator;

class ResultAbstract
{
    protected $response = null;
    protected $dataForValidation = null;
    protected $rules = [];
    protected $validationMessage = null;
    protected $validationRules = [];

    public function __construct($response)
    {
        $this->response = \GuzzleHttp\json_decode($response);

        $this->dataForValidation = json_decode($response, JSON_OBJECT_AS_ARRAY);

        $this->setValidationRules();
        $this->validate();
    }

    public function getResponse()
    {
        return $this->response;
    }

    protected function validate()
    {
        $data = $this->dataForValidation;
        $validator = Validator::make($data, $this->getValidationRules());

        if ($validator->fails()) {
            throw new ResultValidationException($validator);
        }

    }

    protected function setValidationRules() {}

    protected function getValidationRules()
    {
        return $this->rules;
    }
}