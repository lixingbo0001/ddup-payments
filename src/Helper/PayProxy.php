<?php

namespace Ddup\Payments\Helper;

use Ddup\Part\Libs\Obj;
use Ddup\Payments\Config\PaymentNotifyStruct;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Contracts\PaymentInterface;
use Illuminate\Support\Collection;

class PayProxy implements PaymentInterface
{

    private $paymentApplication;

    private $name;

    public function __construct(PaymentInterface $paymentApplication)
    {
        $this->paymentApplication = $paymentApplication;

        $this->setName();
    }

    private function setName()
    {
        $this->name = strtolower(Obj::name($this->paymentApplication));
    }

    private function action($method)
    {
        return $this->name . '.' . $method;
    }

    public function cancel($name, $order)
    {
        return $this->paymentApplication->close($name, $order);
    }

    public function close($name, $order)
    {
        return $this->paymentApplication->close($name, $order);
    }

    public function find($name, Collection $order):Collection
    {
        return $this->paymentApplication->find($name, $order);
    }

    public function pay($gateway, PayOrderStruct $order):Collection
    {
        $name = $this->action(__FUNCTION__);

        MoneyFilter::before($name, $order);

        $result = $this->paymentApplication->pay($gateway, $order);

        MoneyFilter::after($name, $result);

        return $result;
    }

    public function refund($name, Collection $order):Collection
    {
        $action_name = $this->action(__FUNCTION__);

        MoneyFilter::before($action_name, $order);

        $result = $this->paymentApplication->refund($name, $order);

        MoneyFilter::after($action_name, $result);

        return $result;
    }

    public function success()
    {
        return $this->paymentApplication->success();
    }

    public function verify():Collection
    {
        $result = $this->paymentApplication->verify();

        return $result;
    }

    public function callbackConversion($data):PaymentNotifyStruct
    {
        $name = $this->action(__FUNCTION__);

        $result = $this->paymentApplication->callbackConversion($data);

        $result = new Collection($result->toArray());

        MoneyFilter::after($name, $result);

        return new PaymentNotifyStruct($result);
    }
}