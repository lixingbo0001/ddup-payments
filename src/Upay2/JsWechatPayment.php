<?php

namespace Ddup\Payments\Upay2;


use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Upay2\Kernel\UpayPay;
use Illuminate\Support\Collection;

class JsWechatPayment extends UpayPay implements PayableInterface
{
    function pay(array $payload, PayOrderStruct $order):Collection
    {
        return $this->payRequest($payload, $order);
    }

    public function msgType()
    {
        return 'bills.getQRCode';
    }

    public function instMid()
    {
        return 'QRPAYDEFAULT';
    }

}