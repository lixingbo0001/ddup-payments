<?php

namespace Ddup\Payments;

use Ddup\Part\Libs\Time;
use Ddup\Payments\Config\PaymentNotifyStruct;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Config\RefundOrderStruct;
use Ddup\Payments\Config\SdkStruct;
use Ddup\Payments\Contracts\PaymentInterface;
use Ddup\Payments\Exceptions\PayApiException;
use Ddup\Payments\Helper\MakePaymentTrait;
use Ddup\Payments\Helper\Application;
use Ddup\Payments\Upay2\Kernel\UpayClient;
use Ddup\Payments\Upay2\Kernel\UpayConfig;
use Ddup\Payments\Upay2\Kernel\UpayStructTransform;
use Illuminate\Http\Request;
use Ddup\Payments\Upay2\Kernel\Support;
use Illuminate\Support\Collection;

class Upay2 implements PaymentInterface
{

    use MakePaymentTrait;


    protected $config;

    private $app;

    public function __construct(Application $app)
    {
        $this->app    = $app;
        $this->config = new UpayConfig($app->config);
    }

    public function isYuan()
    {
        return false;
    }

    public function payload()
    {
        //如果sign_type="md5"的时候不传，否则会报签名错误

        return [
            'msgSrc'           => $this->config->msg_src,
            'mid'              => $this->config->merchant_id,
            'tid'              => $this->config->terminal_id,
            'requestTimestamp' => Time::date(),
        ];
    }

    private function getClient()
    {
        return new UpayClient($this->app, $this->config);
    }

    private function getHandle($name)
    {
        return $this->makePay(__CLASS__, $name, $this->app, $this->config);
    }

    public function find($name, PayOrderStruct $order):Collection
    {
        $params = $this->payload();

        $params = Support::paraFilter($params);
        $result = $this->getClient()->requestApi('', $params);

        return new Collection($result);
    }

    public function pay($name, PayOrderStruct $order):PayOrderStruct
    {
        $result = $this->getHandle($name)->pay($this->payload(), $order);

        $order->transaction_id = $result->get('transaction_id');
        $order->qr_code        = $result->get('qr_code');
        $order->sdk            = new SdkStruct($result->get('sdk_param'));

        return $order;
    }

    public function refund($name, RefundOrderStruct $order):RefundOrderStruct
    {
        $instance = $this->makePay(__CLASS__, $name, $this->app, $this->config);

        $params = $this->payload();

        $params['msgType']       = 'bills.refund';
        $params['msgSrc']        = Support::msgSrc($this->config);
        $params['instMid']       = $instance->getTradeType();
        $params['billDate']      = $order->get('created_at');
        $params['billNo']        = $order->order_no;
        $params['refundOrderId'] = $order->refund_no;
        $params['refundAmount']  = $order->refund_amount;

        $params         = Support::paraFilter($params);
        $params['sign'] = Support::generateSign($params, $this->config->key);

        $result = $this->getClient()->requestApi('', $params);

        $order->transaction_id    = $result->get('refundOrderId', '');
        $order->channel_refund_id = $result->get('refundTargetOrderId', '');

        return $order;
    }

    public function success()
    {
        return "SUCCESS";
    }

    public function verify():Collection
    {
        $data = Request::createFromGlobals()->all();

        if (!$data) {
            throw  new PayApiException("银联通道异步通知出错:没返回数据", PayApiException::api_error);
        }

        return new Collection($data);
    }

    public function callbackConversion($data):PaymentNotifyStruct
    {
        return new PaymentNotifyStruct($data, new UpayStructTransform());
    }
}