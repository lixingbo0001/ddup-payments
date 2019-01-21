<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/21
 * Time: 下午9:22
 */

namespace Ddup\Payments\Fuyou\Kernel;


use Ddup\Part\Libs\Str;
use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Helper\Application;
use Ddup\Payments\Upay\Kernel\UpayClient;
use Ddup\Payments\Upay\Kernel\UpayConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

abstract class Pay implements PayableInterface
{
    protected $config;
    private   $client;

    public function __construct(Application $app, UpayConfig $config)
    {
        $this->config = $config;
        $this->client = new UpayClient($app, $config);
    }

    private function setCommonParam(Array $payload, Collection $params)
    {
        $payload = array_merge([
            'version'                => $this->config->version,
            'ins_cd'                 => '',
            'mchnt_cd'               => '',
            'term_id'                => '',
            'random_str'             => '',
            'goods_des'              => '',
            'goods_detail'           => '',
            'goods_tag'              => '',
            'product_id'             => '',
            'addn_inf'               => '',
            'mchnt_order_no'         => '',
            'curr_type'              => 'CNY',
            'order_amt'              => '',
            'term_ip'                => '',
            'txn_begin_ts'           => '',
            'notify_url'             => $this->config->notify_url,
            'limit_pay'              => '',
            'trade_type'             => 'FWC',
            'openid'                 => '',
            'sub_openid'             => '',
            'sub_appid'              => '',
            'reserved_expire_minute' => '5',
            'reserved_fy_term_id'    => '',
            'reserved_fy_term_type'  => '',
            'reserved_txn_bonus'     => '',
            'reserved_fy_term_sn'    => '',
        ], $payload);

        $sign = '';

        $payload['sign'] = $sign;

        return $payload;
    }

    function pay(Array $payload, Collection $params):Collection
    {
        $payload = $this->preOrder($payload, $params);
        $payload = $this->setCommonParam($payload, $params);

        //        $payload['mchnt_cd']= $params['is_ordinary'];

        //        'mchnt_cd' => (isset($paymentInfo['is_ordinary']) && $paymentInfo['is_ordinary']) ? $configParam['OrdinaryMechNum'] : $configParam['MechNum'],

        $payload['random_str']     = Str::rand(20);
        $payload['mchnt_order_no'] = $params['order_no'];
        $payload['goods_des']      = $params['subject'];
        $payload['order_amt']      = $params['amount'];
        $payload['order_amt']      = Request::createFromGlobals()->server->get('REMOTE_ADDR');
        $payload['txn_begin_ts']   = date('YmdHis');

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