<?php

namespace Ddup\Payments\Upay;


use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Upay\Kernel\UpayPay;
use Illuminate\Support\Collection;

class MicroAlipayPayment extends UpayPay implements PayableInterface
{
    function pay(array $payload, PayOrderStruct $order):Collection
    {
        return parent::payRequest($payload, $order);
    }

    function bizContent(array $params)
    {
        $bizContent = [
            'ext_no'       => $params['order_no'],
            'auth_code'    => $params['auth_code'],
            'subject'      => $params['subject'],
            'total_amount' => $params['amount'],
            'currency'     => 'CNY',
        ];

        return $bizContent;
    }

    public function getChannel()
    {
        return 'umszj.channel.alipay';
    }

    public function getTradeType()
    {
        return 'umszj.trade.pay';
    }
}