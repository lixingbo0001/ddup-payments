<?php

namespace Ddup\Payments\Contracts;

use Ddup\Payments\Config\PaymentNotifyStruct;
use Illuminate\Support\Collection;

interface PaymentInterface
{
    public function pay($name, Collection $params):Collection;

    public function find($name, Collection $order):Collection;

    public function refund($name, Collection $order):Collection;

    public function cancel($name, $order);

    public function close($name, $order);

    public function verify():Collection;

    public function success();

    public function callbackConversion($data):PaymentNotifyStruct;
}

