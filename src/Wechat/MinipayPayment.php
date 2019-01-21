<?php

namespace Ddup\Payments\Wechat;

use Ddup\Part\Libs\Str;
use Ddup\Payments\Wechat\Kernel\Pay;
use Illuminate\Support\Collection;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Wechat\Kernel\JsApi;


class MinipayPayment extends Pay implements PayableInterface
{


    function preOrder(Array $payload, Collection $params)
    {
        return $payload;
    }

    function after(Collection $result):Collection
    {
        $prepay_id = $result->get('prepay_id');

        $js_api_param = [
            'appId'     => $this->config->app_id,
            'timeStamp' => (string)time(),
            'nonceStr'  => Str::rand(20),
            'package'   => "prepay_id={$prepay_id}",
            'signType'  => 'MD5'
        ];

        $jsApi = new JsApi();

        $js_api_param['paySign'] = $jsApi->sign($js_api_param, $this->config->key);

        return new Collection(compact('prepay_id', 'js_api_param'));
    }

    function getTradeType()
    {
        return 'JSAPI';
    }

    public function endPoint()
    {
        return 'pay/unifiedorder';
    }

}