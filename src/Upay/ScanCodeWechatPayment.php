<?php

namespace Ddup\Payments\Upay;


use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Upay\Kernel\UpayPay;
use Illuminate\Support\Collection;

class ScanCodeWechatPayment extends UpayPay implements PayableInterface
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
        return 'umszj.trade.precreate';
    }

    function bizContent(PayOrderStruct $order)
    {
        return [
            'ext_no'          => $order->order_no,
            'subject'         => $order->subject,
            'goods_detail'    => $order->subject,
            'total_amount'    => $order->amount,
            'body'            => '',
            'currency'        => 'CNY',
            'timeout_express' => '15m',
            'qr_code_enable'  => 'N'
        ];
    }


}