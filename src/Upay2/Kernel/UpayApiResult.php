<?php

namespace Ddup\Payments\Upay2\Kernel;


use Ddup\Part\Api\ApiResultInterface;
use Illuminate\Support\Collection;

class UpayApiResult implements ApiResultInterface
{

    private $_result;

    public function __construct($ret)
    {
        if (!$ret) {
            $ret = ['errCode' => -500, 'errMsg' => '接口返回空字符串'];
        }

        if (is_string($ret)) {
            $ret = json_decode($ret, true);
        }

        $this->_result = new Collection($ret);
    }

    function isSuccess()
    {
        return strtoupper($this->getCode()) == 'SUCCESS';
    }

    function getCode()
    {
        return $this->_result->get('errCode');
    }

    function getMsg()
    {
        return $this->_result->get('errMsg');
    }

    function getData():Collection
    {
        return $this->_result;
    }

}