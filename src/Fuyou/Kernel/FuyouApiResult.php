<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Fuyou\Kernel;


use Ddup\Part\Api\ApiResultInterface;
use Ddup\Part\Libs\Helper;
use Ddup\Part\Message\MsgFromXml;
use Illuminate\Support\Collection;

class FuyouApiResult implements ApiResultInterface
{

    private $_result;

    public function __construct($ret)
    {
        if (!$ret) {
            $result = ['result_code' => -500, 'result_msg' => '富友接口返回空'];
        } else {
            $result = new MsgFromXml($ret);
        }

        $this->_result = Helper::toCollection($result);
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