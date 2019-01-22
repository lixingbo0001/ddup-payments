<?php

namespace Ddup\Payments;

use Ddup\Payments\Config\PaymentNotifyStruct;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Config\RefundOrderStruct;
use Ddup\Payments\Contracts\PaymentInterface;
use Ddup\Payments\Exceptions\PayApiException;
use Ddup\Payments\Helper\MakePaymentTrait;
use Ddup\Payments\Helper\Application;
use Ddup\Payments\Upay\Kernel\UpayClient;
use Ddup\Payments\Upay\Kernel\UpayConfig;
use Ddup\Payments\Upay\Kernel\UpayStructTransform;
use Illuminate\Http\Request;
use Ddup\Payments\Upay\Kernel\Support;
use Illuminate\Support\Collection;

class Upay implements PaymentInterface
{

    use MakePaymentTrait;


    protected $config;

    private $app;

    public function __construct(Application $app)
    {
        $this->app    = $app;
        $this->config = new UpayConfig($app->config);
    }

    public function payload()
    {
        return [
            'merchant_id'     => $this->config->merchant_id,
            'notify_url'      => $this->config->notify_url,
            'version'         => $this->config->version,
            'terminal_id'     => $this->config->terminal_id,
            'sign_type'       => $this->config->sign_type,
            'timestamp'       => date('Y-m-d H:i:s'),
            'request_id'      => Support::getRequestId($this->config->terminal_id),
            'term_request_id' => Support::getTermRequestId($this->config->terminal_id)
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

    private function sendBizContent(&$params, $channel, $bizType, $bizContent)
    {
        $params['biz_channel'] = $channel;
        $params['biz_type']    = $bizType;
        $params['biz_content'] = json_encode($bizContent, JSON_UNESCAPED_UNICODE);
    }

    private function getBizContent(Collection $result)
    {
        $reBizContent = json_decode($result->get('biz_content'), true);
        return new Collection($reBizContent);
    }

    public function find($name, PayOrderStruct $order):Collection
    {
        $orderCollect = new Collection($order);
        $params       = $this->payload();

        $bizContent = [
            "type"   => "common",
            'ext_no' => $orderCollect->get('order_no'),
        ];

        $this->sendBizContent($params, 'umszj.channel.common', 'umszj.trade.query', $bizContent);

        $params = Support::filterPayload($params, $this->config);
        $result = $this->getClient()->requestApi('', $params);

        return new Collection($result);
    }


    public function pay($name, PayOrderStruct $order):Collection
    {
        return $this->getHandle($name)->pay($this->payload(), $order);
    }

    public function refund($name, RefundOrderStruct $order):Collection
    {
        $orderCollect = new Collection($order);
        $params       = $this->payload();

        $bizContent = [
            'ext_no'          => $orderCollect->get('order_no'),
            'trade_no'        => $orderCollect->get('transaction_id'),
            'refund_amount'   => $orderCollect->get('amount'),
            'refund_trade_no' => $orderCollect->get('refund_no'),
            'currency'        => 'CNY',
        ];

        $this->sendBizContent($params, $this->getHandle($name)->getChannel(), 'umszj.trade.refund', $bizContent);

        $params       = Support::filterPayload($params, $this->config);
        $result       = $this->getClient()->requestApi('', $params);
        $reBizContent = $this->getBizContent($result);

        $return = [
            "transaction_id"    => $reBizContent->get('trade_no'),
            "openid"            => $reBizContent->get('buyer_id'),
            "attach"            => $reBizContent->get('attach'),
            "channel_refund_id" => $reBizContent->get('back_trade_no')
        ];

        return new Collection($return);
    }

    public function success()
    {
        return "success";
    }

    public function verify():Collection
    {
        $data = Request::createFromGlobals()->all();

        if (!$data) {
            throw  new PayApiException("Upay notify error:not data", PayApiException::api_error);
        }

        return new Collection($data);
    }

    public function callbackConversion($data):PaymentNotifyStruct
    {
        return new PaymentNotifyStruct($data, new UpayStructTransform());
    }
}