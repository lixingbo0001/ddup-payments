<?php

namespace Ddup\Payments\Wechat;

use Ddup\Part\Libs\Str;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Wechat\Kernel\WechatPay;
use Ddup\Payments\Wechat\Kernel\Support;
use Illuminate\Support\Collection;
use Ddup\Payments\Contracts\PayableInterface;

class JsApiPayment extends WechatPay implements PayableInterface
{
    function pay(array $payload, PayOrderStruct $order):Collection
    {
        $result = parent::payRequest($payload, $order);

        $prepay_id = $result->get('prepay_id');

        $js_api_param = [
            'appId'     => $this->config->app_id,
            'timeStamp' => (string)time(),
            'nonceStr'  => Str::rand(20),
            'package'   => "prepay_id={$prepay_id}",
            'signType'  => 'MD5'
        ];

        $js_api_param['paySign'] = Support::jsApiSign($js_api_param, $this->config->key);

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