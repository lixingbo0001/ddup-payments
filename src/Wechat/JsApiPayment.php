<?php

namespace Ddup\Payments\Wechat;

use Ddup\Part\Libs\Str;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Config\SdkStruct;
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

        $sdk = new SdkStruct();

        $sdk->appId     = $this->config->app_id;
        $sdk->timeStamp = (string)time();
        $sdk->nonceStr  = Str::rand(20);
        $sdk->package   = "prepay_id={$prepay_id}";
        $sdk->signType  = 'MD5';
        $sdk->paySign   = Support::jsApiSign($sdk->toArray(), $this->config->key);

        $result->offsetSet('sdk_param', $sdk->toArray());
        $result->offsetSet('prepay_id', $sdk->toArray());

        return $result;
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