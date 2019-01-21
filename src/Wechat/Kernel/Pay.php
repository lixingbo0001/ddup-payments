<?php

namespace Ddup\Payments\Wechat\Kernel;


use Ddup\Part\Libs\Time;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Helper\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class Pay implements PayableInterface
{

    protected $config;
    private   $client;

    public function __construct(Application $app, WechatConfig $config)
    {
        $this->config = $config;
        $this->client = new WechatClient($app, $config);
    }

    private function setCommonParam(Array $payload, Collection $params)
    {
        $payload['trade_type']       = $this->getTradeType();
        $payload['body']             = $params->get('subject', '商品');
        $payload['out_trade_no']     = $params->get('order_no');
        $payload['total_fee']        = $params->get('amount');
        $payload['openid']           = $params->get('openid');
        $payload['time_expire']      = Time::formatReset('YmdHis', $params->get('expired_at'));
        $payload['spbill_create_ip'] = Request::createFromGlobals()->server->get('SERVER_ADDR');

        $sign = Support::generateSign($payload, $this->config->key);

        $payload['sign'] = $sign;

        return $payload;
    }

    function pay(Array $payload, Collection $params):Collection
    {
        $payload = $this->preOrder($payload, $params);
        $payload = $this->setCommonParam($payload, $params);

        $result = $this->client->requestApi($this->endPoint(), $payload);

        return $this->after($result);
    }

    function getChannel()
    {
        return '';
    }

    function after(Collection $result):Collection
    {
        return $result;
    }

    abstract function preOrder(Array $payload, Collection $params);

    abstract function endPoint();

}
