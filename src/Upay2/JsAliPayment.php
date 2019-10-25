<?php

namespace Ddup\Payments\Upay2;


use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Upay2\Kernel\UpayPay;
use Illuminate\Support\Collection;

class JsAliPayment extends UpayPay implements PayableInterface
{
    function pay(array $payload, PayOrderStruct $order):Collection
    {
        return $this->payRequest($payload, $order);
    }

    public function actionType()
    {
        return 'bills.getQRCode';
    }

    public function getTradeType()
    {
        return 'QRPAYDEFAULT';
    }

}