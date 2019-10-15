<?php

namespace Ddup\Payments\Helper;

use Ddup\Part\Libs\Unit;
use Ddup\Payments\Config\PaymentNotifyStruct;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Config\RefundOrderStruct;
use Ddup\Payments\Contracts\PaymentInterface;
use Ddup\Payments\Contracts\PaymentProxyAble;
use Illuminate\Support\Collection;

class PayProxy implements PaymentProxyAble
{

    /**
     * @var PaymentInterface
     */
    private $api;
    private $app;

    public function __construct(Application $container)
    {
        $this->app = $container;
    }

    public function setPayment(PaymentInterface $api)
    {
        $this->api = $api;
    }

    public function isYuan()
    {
        return $this->api->isYuan();
    }

    private function moneyBefore($amount)
    {
        if ($this->api->isYuan()) {
            return $amount;
        }

        return Unit::yuntoFen($amount);
    }

    private function moneyAfter($amount)
    {
        if ($this->api->isYuan()) {
            return $amount;
        }

        return Unit::fentoYun($amount);
    }

    public function find($name, PayOrderStruct $order):Collection
    {
        $order->amount = $this->moneyBefore($order->amount);

        $result = $this->api->find($name, $order);

        return $result;
    }

    public function pay($gateway, PayOrderStruct $order):PayOrderStruct
    {
        $order->amount           = $this->moneyBefore($order->amount);
        $order->separate_account = $this->moneyBefore($order->separate_account);

        $result = $this->api->pay($gateway, $order);

        $result->amount           = $this->moneyAfter($result->amount);
        $result->separate_account = $this->moneyAfter($result->separate_account);

        return $result;
    }

    public function refund($name, RefundOrderStruct $order):RefundOrderStruct
    {
        $order->amount        = $this->moneyBefore($order->amount);
        $order->refund_amount = $this->moneyBefore($order->refund_amount);

        $result = $this->api->refund($name, $order);

        $result->amount        = $this->moneyAfter($result->amount);
        $result->refund_amount = $this->moneyAfter($result->refund_amount);

        return $result;
    }

    public function success()
    {
        return $this->api->success();
    }

    public function verify():Collection
    {
        $result = $this->api->verify();

        return $result;
    }

    public function callbackConversion($data):PaymentNotifyStruct
    {
        $result = $this->api->callbackConversion($data);

        $result->amount       = $this->moneyAfter($result->amount);
        $result->total_amount = $this->moneyAfter($result->total_amount);

        return $result;
    }
}