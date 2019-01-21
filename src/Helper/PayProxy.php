<?php

namespace Ddup\Payments\Helper;

use Ddup\Part\Libs\Obj;
use Ddup\Payments\Contracts\PaymentApplicationInterface;
use Illuminate\Support\Collection;

class PayProxy implements PaymentApplicationInterface
{

    private $paymentApplication;

    private $name;

    public function __construct(PaymentApplicationInterface $paymentApplication)
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

    public function pay($gateway, Collection $params):Collection
    {
        $name = $this->action(__FUNCTION__);

        MoneyFilter::before($name, $params);

        $result = $this->paymentApplication->pay($gateway, $params);

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

    public function callbackConversion($data):Collection
    {
        $name = $this->action(__FUNCTION__);

        $data = $this->paymentApplication->callbackConversion($data);

        $result = new Collection($data);

        MoneyFilter::after($name, $result);

        return $result;
    }
}