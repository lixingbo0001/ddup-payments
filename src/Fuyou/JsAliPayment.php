<?php

namespace Ddup\Payments\Fuyou;

use Ddup\Part\Libs\Arr;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Fuyou\Kernel\FuyouPay;
use Illuminate\Support\Collection;

class JsAliPayment extends FuyouPay implements PayableInterface
{
    function getTradeType()
    {
        return 'FWC';
    }

    function endPoint()
    {
        return 'wxPreCreate';
    }

    function prepay($payload)
    {
        return Arr::getIfExists($payload, self::jsField());
    }

    function after(Collection $result):Collection
    {
        return parent::withSdk($result);
    }

}