<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Fuyou\Kernel;


use Ddup\Part\Api\ApiResultInterface;
use Illuminate\Support\Collection;

class FuyouApiResult implements ApiResultInterface
{

    private $_result;

    public function __construct($ret)
    {
        $this->_result = new Collection($ret);
    }

    function isSuccess()
    {
        return $this->getCode() == '000000';
    }

    function getCode()
    {
        return $this->_result->get('result_code');
    }

    function getMsg()
    {
        return $this->_result->get('result_msg', '');
    }

    function getData():Collection
    {
        return $this->_result;
    }


}