<?php

namespace Ddup\Payments\Wechat;

use Ddup\Part\Libs\Str;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Wechat\Kernel\Support;
use Ddup\Payments\Wechat\Kernel\WechatPay;
use Illuminate\Support\Collection;
use Ddup\Payments\Contracts\PayableInterface;


class MinipayPayment extends WechatPay implements PayableInterface
{
    function pay(array $payload, PayOrderStruct $order):Collection
    {
        $result = parent::payRequest($payload, $order);

        $prepay_id = $result->get('prepay_id');

        $sdk_param = [
            'appId'     => $this->config->app_id,
            'timeStamp' => (string)time(),
            'nonceStr'  => Str::rand(20),
            'package'   => "prepay_id={$prepay_id}",
            'signType'  => 'MD5'
        ];

        $sdk_param['paySign'] = Support::jsApiSign($sdk_param, $this->config->key);

        return new Collection(compact('prepay_id', 'sdk_param'));
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