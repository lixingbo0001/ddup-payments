<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/23
 * Time: 下午4:37
 */

namespace Ddup\Payments\Test\Fuyou\Dev;

use Ddup\Part\Libs\OutCli;
use Ddup\Part\Libs\Str;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Test\PaymentTest;

class JsPayTest extends PaymentTest
{

    public function test_wechat()
    {
        $param = [
            'total_amount' => 0.01,
            'amount'       => 0.01,
            'order_no'     => 1211400 . Str::rand(20, range(0, 9)),
            'subject'      => '描述',
            'openid'       => 'oa7_f0l0x6ltthLTI-K_vv6ODh0M',
        ];

        $result = $this->app->create('fuyou',
            [
                'mode'       => 'TEST',
                'pem_key'    => self::pem_key,
                'app_id'     => '08A9999999',
                'mch_id'     => '0002900F0370542',
                'notify_url' => 'http://test.modernmasters.com/index.php/Supplier/User/myResources.html',
            ]
        )->pay('js_wechat', new PayOrderStruct($param));

        OutCli::printLn($result->toArray());

        $this->assertNotEmpty($result->sdk, 'sdk参数不为空');
    }

}