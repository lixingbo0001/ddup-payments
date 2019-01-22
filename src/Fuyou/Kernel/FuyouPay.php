<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/21
 * Time: 下午9:22
 */

namespace Ddup\Payments\Fuyou\Kernel;


use Ddup\Part\Libs\Str;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Helper\Application;
use Illuminate\Http\Request;
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

    private function setCommonParam(Array $payload)
    {
        $payload = array_merge([
            'version'                => $this->config->version,
            'ins_cd'                 => '',
            'mchnt_cd'               => $this->config->mch_id,
            'term_id'                => '88888888',
            'random_str'             => Str::rand(20),
            'goods_des'              => '',
            'goods_detail'           => '',
            'goods_tag'              => '',
            'product_id'             => '',
            'addn_inf'               => '',
            'mchnt_order_no'         => '',
            'curr_type'              => 'CNY',
            'order_amt'              => '',
            'term_ip'                => Request::createFromGlobals()->server->get('REMOTE_ADDR'),
            'txn_begin_ts'           => date('YmdHis'),
            'notify_url'             => $this->config->notify_url,
            'limit_pay'              => '',
            'trade_type'             => 'FWC',
            'openid'                 => '',
            'sub_openid'             => '',
            'sub_appid'              => '',
            'reserved_expire_minute' => $this->config->expire_minute,
            'reserved_fy_term_id'    => '',
            'reserved_fy_term_type'  => '',
            'reserved_txn_bonus'     => '',
            'reserved_fy_term_sn'    => '',
        ], $payload);

        return $payload;
    }

    public function pay(Array $payload, PayOrderStruct $order):Collection
    {
        $payload = $this->setCommonParam($payload);

        $payload['mchnt_order_no'] = $order->order_no;
        $payload['goods_des']      = $order->subject;
        $payload['order_amt']      = $order->amount;

        $payload['sign'] = Support::sign($payload, $this->config->pem_key);

        $this->client->requestApi($this->endPoint(), $payload);

        return $this->client->result->getData();
    }

    function getChannel()
    {
        return '';
    }

    abstract function endPoint();
}