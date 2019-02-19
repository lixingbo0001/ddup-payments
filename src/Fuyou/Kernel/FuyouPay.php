<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/21
 * Time: 下午9:22
 */

namespace Ddup\Payments\Fuyou\Kernel;


use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Helper\Application;
use Illuminate\Support\Collection;

abstract class FuyouPay implements PayableInterface
{
    protected $config;
    private   $client;

    public function __construct(Application $app, FuyouConfig $config)
    {
        $this->config = new FuyouConfig($config);
        $this->client = new FuyouClient($app, $config);
    }

    private function setCommonParam(array $payload, PayOrderStruct $order)
    {
        $payload = array_merge([
            "addn_inf"               => "",
            "openid"                 => $order->get('openid'),
            'order_type'             => $this->getTradeType(),
            'reserved_expire_minute' => $this->config->expire_minute,
            'mchnt_order_no'         => $order->order_no,
            'goods_des'              => $order->subject,
            'order_amt'              => $order->amount,
            'goods_detail'           => '',
            'goods_tag'              => ''
        ], $payload);

        return $payload;
    }

    public function pay(array $payload, PayOrderStruct $order):Collection
    {
        $payload = $this->setCommonParam($payload, $order);

        $payload['sign'] = Support::sign($payload, $this->config->pem_key);

        $this->client->requestApi($this->endPoint(), $payload);

        return $this->client->result()->getData();
    }

    function getChannel()
    {
        return '';
    }

    abstract function endPoint();
}