<?php

namespace Ddup\Payments\Fuyou;

use Ddup\Part\Libs\Arr;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Fuyou\Kernel\FuyouPay;

class JsWechatPayment extends FuyouPay implements PayableInterface
{

    function getTradeType()
    {
        return 'WECHAT';
    }

    function endPoint()
    {
        return 'preCreate';
    }

    function prepay($payload)
    {
        return Arr::getIfExists($payload, self::jsField());
    }

}