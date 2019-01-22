<?php

namespace Ddup\Payments\Fuyou;

use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Fuyou\Kernel\FuyouPay;

class JsAliPayment extends FuyouPay implements PayableInterface
{
    function getTradeType()
    {
        return 'ALIPAY';
    }

    function endPoint()
    {
        return 'preCreate';
    }

}