<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/9/30
 * Time: 下午3:56
 */

namespace Ddup\Payments\Test\Upay2\Support;


use Ddup\Part\Request\HasHttpRequest;

class Client
{
    use HasHttpRequest;

    function getBaseUri()
    {
        return 'https://qr-test2.chinaums.com/netpay-route-server/api/';
    }

    function requestOptions()
    {
        return [];
    }

    function requestParams()
    {
        return [];
    }

}