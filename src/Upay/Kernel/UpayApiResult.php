<?php

namespace Ddup\Payments\Upay\Kernel;


use Ddup\Part\Api\ApiResultInterface;
use Illuminate\Support\Collection;

class UpayApiResult implements ApiResultInterface
{

    private $_result;

    public function __construct($ret)
    {
        $this->_result = new Collection($ret);
    }

    function isSuccess()
    {
        return $this->getCode() == 100 && $this->_result->get('sub_code') == 100;
    }

    function getCode()
    {
        return $this->_result->get('code');
    }

    function getMsg()
    {
        $message     = $this->_result->get('message');
        $sub_message = $this->_result->get('sub_message');

        if ($sub_message) {
            $message .= '备注:' . $sub_message;
        }

        return $message;
    }

    function getData():Collection
    {
        return $this->_result;
    }

}