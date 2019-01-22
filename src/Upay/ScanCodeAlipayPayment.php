<?php

namespace Ddup\Payments\Upay;


use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Upay\Kernel\UpayPay;
use Illuminate\Support\Collection;

class ScanCodeAlipayPayment extends UpayPay implements PayableInterface
{
    function pay(array $payload, PayOrderStruct $order):Collection
    {
        return parent::payRequest($payload, $order);
    }

    function bizContent(array $params)
    {
        $bizContent = [
            'ext_no'          => $params['order_no'],
            'subject'         => $params['subject'],
            'body'            => '',
            'goods_detail'    => 'goods_detail',
            'total_amount'    => $params['amount'],
            'currency'        => 'CNY',
            'timeout_express' => '15m',
            'qr_code_enable'  => 'N'
        ];

        return $bizContent;
    }


    public function getChannel()
    {
        return 'umszj.channel.alipay';
    }

    public function getTradeType()
    {
        return 'umszj.trade.precreate';
    }

}