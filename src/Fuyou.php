<?php

namespace Ddup\Payments;

use Ddup\Part\Libs\Str;
use Ddup\Part\Message\MsgFromXml;
use Ddup\Payments\Config\PaymentNotifyStruct;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Config\RefundOrderStruct;
use Ddup\Payments\Config\SdkStruct;
use Ddup\Payments\Contracts\PaymentInterface;
use Ddup\Payments\Exceptions\PayApiException;
use Ddup\Payments\Fuyou\Kernel\FuyouConfig;
use Ddup\Payments\Helper\MakePaymentTrait;
use Ddup\Payments\Helper\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Fuyou implements PaymentInterface
{

    use MakePaymentTrait;

    protected $config;

    private $app;

    public function __construct(Application $app)
    {
        $this->app    = $app;
        $this->config = new FuyouConfig($app->config);
    }

    public function isYuan()
    {
        return false;
    }

    public function payload()
    {
        $payload = [
            'ins_cd'       => $this->config->app_id,
            'mchnt_cd'     => $this->config->mch_id,
            'notify_url'   => $this->config->notify_url,
            'version'      => $this->config->version,
            'curr_type'    => 'CNY',
            'random_str'   => Str::rand(20),
            'txn_begin_ts' => date('YmdHis'),
            'term_id'      => '88888888',
            'term_ip'      => Request::createFromGlobals()->server->get('REMOTE_ADDR', '117.29.110.187'),
        ];

        return $payload;
    }

    public function find($name, PayOrderStruct $order):Collection
    {
        return new Collection();
    }

    public function pay($name, PayOrderStruct $order):PayOrderStruct
    {
        $result = $this->makePay(__CLASS__, $name, $this->app, $this->config)->pay($this->payload(), $order);

        $order->transaction_id = $result->get('transaction_id', '');
        $order->qr_code        = $result->get('qr_code');

        $order->sdk = new SdkStruct($result->get('sdk_param'));

        return $order;
    }

    public function refund($name, RefundOrderStruct $order):RefundOrderStruct
    {
        return $order;
    }

    public function success()
    {
        return 1;
    }

    public function verify():Collection
    {
        $req = Request::createFromGlobals()->input('req');

        if (!$req) {
            throw  new PayApiException("富友通道异步通知出错:没返回数据", PayApiException::api_error);
        }

        $xml = urldecode($req);

        $result = new MsgFromXml($xml);

        return new Collection($result->toArray());
    }

    public function callbackConversion($data):PaymentNotifyStruct
    {
        return new PaymentNotifyStruct($data);
    }
}