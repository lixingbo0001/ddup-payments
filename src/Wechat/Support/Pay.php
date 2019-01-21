<?php

namespace Ddup\Payments\Wechat\Support;


use Ddup\Part\Libs\Time;
use Ddup\Payments\Contracts\PayableInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class Pay implements PayableInterface
{

    protected $config;

    public function __construct(WechatConfig $config)
    {
        $this->config = $config;
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

        $result = Support::requestApi($this->endPoint(), $payload, $this->config);

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
