<?php

namespace Ddup\Payments\Upay;


use Ddup\Payments\Contracts\PayableInterface;
use Illuminate\Support\Collection;
use Ddup\Payments\Upay\Kernel\Pay;

class ScanCodeAlipayPayment extends Pay implements PayableInterface
{


    public function getChannel()
    {
        return 'umszj.channel.alipay';
    }

    public function getTradeType()
    {
        return 'umszj.trade.precreate';
    }

    public function prePay(Array $params)
    {
        $bizContent = ['ext_no' => $params['order_no'], 'subject' => $params['subject'], 'body' => '', 'goods_detail' => 'goods_detail', 'total_amount' => $params['amount'], 'currency' => 'CNY', 'timeout_express' => '15m', 'qr_code_enable' => 'N',];

        return $bizContent;
    }

    public function after(Collection $result):Collection
    {
        $result->offsetSet('api_result', $result->all());

        return parent::after($result);
    }

}