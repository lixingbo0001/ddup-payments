<?php

namespace Ddup\Payments\Wechat;

use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Wechat\Kernel\WechatPay;
use Illuminate\Support\Collection;

class MicroPayment extends WechatPay implements PayableInterface
{
    function pay(array $payload, PayOrderStruct $order):Collection
    {
        return parent::payRequest($payload, $order);
    }

    function getTradeType()
    {
        return 'MICROPAY';
    }

    function endPoint()
    {
        return 'pay/micropay';
    }

}