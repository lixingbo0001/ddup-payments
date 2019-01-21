<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/10/1
 * Time: 上午10:20
 */

namespace Ddup\Payments\Wechat\Support;


class JsApi
{
    private $sign_string;

    function sign($js_api_param, $key)
    {
        ksort($js_api_param);

        $js_api_param['key'] = $key;

        $tmp = [];

        foreach ($js_api_param as $k => $v) {
            $tmp[] = "{$k}={$v}";
        }

        $this->sign_string = join('&', $tmp);

        return strtoupper(md5($this->sign_string));
    }

    function getSignString()
    {
        return $this->sign_string;
    }
}