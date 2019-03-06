<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/21
 * Time: 下午9:22
 */

namespace Ddup\Payments\Fuyou\Kernel;


use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Config\SdkStruct;
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

    /**
     * @return array
     */
    abstract function endPoint();

    abstract function prepay($payload);

    abstract function after(Collection $result):Collection;

    function getChannel()
    {
        return '';
    }

    private function baseField()
    {
        return [
            "ins_cd",
            "mchnt_cd",
            "notify_url",
            "version",
            "curr_type",
            "random_str",
            "txn_begin_ts",
            "term_id",
            "term_ip",
            'goods_des',
            'goods_detail',
            'goods_tag',
            'mchnt_order_no',
            'addn_inf',
            'curr_type',
            'order_amt',
        ];
    }

    protected function dyQrField()
    {
        return array_merge(self::baseField(), [
            'order_type',
        ]);
    }

    protected function jsField()
    {
        return array_merge(self::baseField(), [
            'product_id',
            'limit_pay',
            'trade_type',
            'openid',
            'sub_openid',
            'sub_appid'
        ]);
    }

    private function fill(array $payload, PayOrderStruct $order)
    {
        $payload = array_merge([
            "addn_inf"               => "",
            "openid"                 => '',
            'order_type'             => $this->getTradeType(),
            'reserved_expire_minute' => $this->config->expire_minute,
            'mchnt_order_no'         => $order->order_no,
            'goods_des'              => iconv('UTF-8', 'GBK', $order->subject),
            'order_amt'              => $order->amount,
            'goods_detail'           => '',
            'goods_tag'              => '',
            'product_id'             => '',
            'limit_pay'              => '',
            'trade_type'             => $this->getTradeType(),
            'sub_openid'             => $order->get('openid', ''),
            'sub_appid'              => $this->config->sub_app_id
        ], $payload);

        return $payload;
    }

    public function pay(array $payload, PayOrderStruct $order):Collection
    {
        $payload = $this->fill($payload, $order);

        $payload = $this->prepay($payload);

        $payload['sign'] = Support::sign($payload, $this->config->pem_key);

        $this->client->requestApi($this->endPoint(), $payload);

        return $this->after($this->client->result()->getData());
    }

    protected function withSdk(Collection $result)
    {
        $sdk = new SdkStruct();

        $sdk->appId     = $result->get('sdk_appid');
        $sdk->timeStamp = $result->get('sdk_timestamp');
        $sdk->nonceStr  = $result->get('sdk_noncestr');
        $sdk->package   = $result->get('sdk_package');
        $sdk->signType  = $result->get('sdk_signtype');
        $sdk->paySign   = $result->get('sdk_paysign');

        $result->offsetSet('sdk_param', $sdk->toArray());

        return $result;
    }


}