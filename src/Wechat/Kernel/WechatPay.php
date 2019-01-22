<?php

namespace Ddup\Payments\Wechat\Kernel;


use Ddup\Part\Libs\Time;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Helper\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class WechatPay implements PayableInterface
{
    protected $config;
    private   $client;

    public function __construct(Application $app, WechatConfig $config)
    {
        $this->config = $config;
        $this->client = new WechatClient($app, $config);
    }

    function getChannel()
    {
        return '';
    }

    function commonData(array $payload, PayOrderStruct $order)
    {
        $payload['trade_type']       = $this->getTradeType();
        $payload['body']             = $order->get('subject', '商品');
        $payload['out_trade_no']     = $order->order_no;
        $payload['total_fee']        = $order->amount;
        $payload['openid']           = $order->openid;
        $payload['time_expire']      = Time::formatReset('YmdHis', $order->expired_at);
        $payload['spbill_create_ip'] = Request::createFromGlobals()->server->get('SERVER_ADDR');

        $sign = Support::generateSign($payload, $this->config->key);

        $payload['sign'] = $sign;

        return $payload;
    }

    public function payRequest(array $payload, PayOrderStruct $order):Collection
    {
        $payload = $this->commonData($payload, $order);
        return $this->client->requestApi($this->endPoint(), $payload);
    }
}
