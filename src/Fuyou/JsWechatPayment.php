<?php

namespace Ddup\Payments\Fuyou;

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

}