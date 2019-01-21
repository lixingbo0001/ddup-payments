<?php

namespace Ddup\Payments\Upay;


use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Upay\Kernel\Pay;

class MicroAlipayPayment extends Pay implements PayableInterface
{


    public function getChannel()
    {
        return 'umszj.channel.alipay';
    }

    public function getTradeType()
    {
        return 'umszj.trade.pay';
    }

    public function prePay(Array $params)
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
}