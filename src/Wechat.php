<?php

namespace Ddup\Payments;

use Ddup\Part\Libs\Str;
use Ddup\Payments\Config\PaymentNotifyStruct;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Config\RefundOrderStruct;
use Ddup\Payments\Contracts\PaymentInterface;
use Ddup\Payments\Exceptions\PayApiException;
use Ddup\Payments\Helper\Application;
use Ddup\Payments\Wechat\Kernel\Support;
use Ddup\Payments\Wechat\Kernel\WechatClient;
use Ddup\Payments\Wechat\Kernel\WechatConfig;
use Ddup\Payments\Wechat\Kernel\WechatStructTransform;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Ddup\Payments\Helper\MakePaymentTrait;


class Wechat implements PaymentInterface
{
    use MakePaymentTrait;

    protected $config;
    private   $app;

    public function __construct(Application $app)
    {
        $this->app    = $app;
        $this->config = new WechatConfig($this->app->config);
    }

    private function payload()
    {
        $payload = [
            'appid'            => $this->config->app_id,
            'mch_id'           => $this->config->mch_id,
            'nonce_str'        => Str::rand(20),
            'notify_url'       => $this->config->notify_url,
            'spbill_create_ip' => Request::createFromGlobals()->getClientIp(),
        ];

        if ($this->config->mode === $this->config::MODE_SERVICE) {
            $payload = array_merge($payload, [
                'sub_mch_id' => $this->config->sub_mch_id,
                'sub_appid'  => $this->config->sub_app_id,
            ]);
        }

        return $payload;
    }

    private function getClient()
    {
        return new WechatClient($this->app, $this->config);
    }

    public function find($name, PayOrderStruct $order):Collection
    {
        return new Collection();
    }

    public function pay($name, PayOrderStruct $order):Collection
    {
        return $this->makePay(__CLASS__, $name, $this->app, $this->config)->pay($this->payload(), $order);
    }

    public function refund($name, RefundOrderStruct $order):Collection
    {
        $params                  = $this->payload();
        $params['total_fee']     = $order->get('amount');
        $params['refund_fee']    = $order->get('refund_amount');
        $params['out_trade_no']  = $order->get('order_no');
        $params['out_refund_no'] = $order->get('refund_no');

        $params['sign'] = Support::generateSign($params, $this->config->key);

        $result = $this->getClient()->safeRequestApi('secapi/pay/refund', $params);

        return new Collection($result);
    }

    public function success()
    {
        return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
    }

    public function verify():Collection
    {
        $request = Request::createFromGlobals();

        $data = Support::fromXml($request->getContent());

        if (Support::generateSign($data, $this->config->key) !== $data['sign']) {

            throw new PayApiException('Wechat Sign Verify FAILED', PayApiException::pay_api_invalid_sign, $data);
        }

        return new Collection($data);
    }

    public function callbackConversion($data):PaymentNotifyStruct
    {
        return new PaymentNotifyStruct($data, new WechatStructTransform());
    }
}