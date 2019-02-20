<?php

namespace Ddup\Payments\Fuyou;

use Ddup\Part\Libs\Arr;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Fuyou\Kernel\FuyouPay;
use Illuminate\Support\Collection;

class DyQrWechatPayment extends FuyouPay implements PayableInterface
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
        return Arr::getIfExists($payload, self::dyQrField());
    }

    function after(Collection $result):Collection
    {
        return $result;
    }


}