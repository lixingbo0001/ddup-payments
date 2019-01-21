<?php

namespace Ddup\Payments;

use Ddup\Part\Libs\Str;
use Ddup\Payments\Config\PaymentNotifyStruct;
use Ddup\Payments\Contracts\PaymentApplicationInterface;
use Ddup\Payments\Helper\Exceptions\PayApiException;
use Ddup\Payments\Wechat\Support\Support;
use Ddup\Payments\Wechat\Support\WechatConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Ddup\Payments\Helper\MakePaymentTrait;


class Wechat implements PaymentApplicationInterface
{
    use MakePaymentTrait;

    protected $config;

    protected $mode;

    protected $payload;

    public function __construct($config)
    {
        $this->config = new WechatConfig($config);

        $this->payload = [
            'appid'            => $this->config->app_id,
            'mch_id'           => $this->config->mch_id,
            'nonce_str'        => Str::rand(20),
            'notify_url'       => $this->config->notify_url,
            'spbill_create_ip' => Request::createFromGlobals()->getClientIp(),
        ];

        if ($this->config->mode === $this->config::MODE_SERVICE) {
            $this->payload = array_merge($this->payload, [
                'sub_mch_id' => $this->config->sub_mch_id,
                'sub_appid'  => $this->config->sub_app_id,
            ]);
        }
    }

    public function cancel($name, $order)
    {
    }

    public function close($name, $order)
    {
    }

    public function find($name, Collection $order):Collection
    {
        return new Collection();
    }

    public function pay($name, Collection $params):Collection
    {
        return $this->makePay(__CLASS__, $name, $this->config)->pay($this->payload, $params);
    }

    public function refund($name, Collection $order):Collection
    {
        $config = clone  $this->config;

        $config->ssl_verify = true;

        $params                  = $this->payload;
        $params['total_fee']     = $order->get('amount');
        $params['refund_fee']    = $order->get('refund_amount');
        $params['out_trade_no']  = $order->get('order_no');
        $params['out_refund_no'] = $order->get('refund_no');

        $params['sign'] = Support::generateSign($params, $config->key);

        $result = Support::requestApi('secapi/pay/refund', $params, $config);

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
        return new PaymentNotifyStruct($data);
    }
}