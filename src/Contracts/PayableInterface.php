<?php

namespace Ddup\Payments\Contracts;

use Ddup\Payments\Config\PayOrderStruct;
use Illuminate\Support\Collection;

interface PayableInterface
{
    function pay(array $payload, PayOrderStruct $order):Collection;

    function getChannel();

    function endPoint();

    function getTradeType();
}

