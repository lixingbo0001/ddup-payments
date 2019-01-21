<?php

namespace Ddup\Payments\Fuyou;

use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Wechat\Kernel\Pay;
use Illuminate\Support\Collection;

class JsApiAliPayment extends Pay implements PayableInterface
{
    function preOrder(Array $payload, Collection $params)
    {
        return $payload;
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