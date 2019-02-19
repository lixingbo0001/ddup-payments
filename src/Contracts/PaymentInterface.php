<?php

namespace Ddup\Payments\Contracts;

use Ddup\Payments\Config\PaymentNotifyStruct;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Config\RefundOrderStruct;
use Illuminate\Support\Collection;

interface PaymentInterface
{
    public function pay($name, PayOrderStruct $order):PayOrderStruct;

    public function find($name, PayOrderStruct $order):Collection;

    public function refund($name, RefundOrderStruct $order):RefundOrderStruct;

    public function verify():Collection;

    public function success();

    public function isYuan();

    public function callbackConversion($data):PaymentNotifyStruct;
}

