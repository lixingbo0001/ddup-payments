<?php

namespace Ddup\Payments\Upay;


use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Upay\Kernel\UpayPay;
use Illuminate\Support\Collection;

class MicroWechatPayment extends UpayPay implements PayableInterface
{
    function pay(array $payload, PayOrderStruct $order):Collection
    {
        return parent::payRequest($payload, $order);
    }

    public function getChannel()
    {
        return 'umszj.channel.wxpay';
    }

    public function getTradeType()
    {
        return 'umszj.trade.pay';
    }

    function bizContent(PayOrderStruct $order)
    {
        return [
            'ext_no'       => $order->order_no,
            'auth_code'    => $order->auth_code,
            'subject'      => $order->subject,
            'total_amount' => $order->amount,
            'currency'     => 'CNY',
        ];
    }
}