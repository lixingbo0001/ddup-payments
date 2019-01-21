<?php

namespace Ddup\Payments\Wechat;

use Ddup\Payments\Contracts\PayableInterface;
use Ddup\Payments\Wechat\Kernel\Pay;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MicroPayment extends Pay implements PayableInterface
{
    function preOrder(Array $payload, Collection $params)
    {
        $payload['spbill_create_ip'] = Request::createFromGlobals()->server->get('SERVER_ADDR');
        return $payload;
    }

    function getTradeType()
    {
        return 'MICROPAY';
    }

    function endPoint()
    {
        return 'pay/micropay';
    }

}