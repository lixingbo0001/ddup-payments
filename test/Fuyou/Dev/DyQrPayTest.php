<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/23
 * Time: 下午4:37
 */

namespace Ddup\Payments\Test\Fuyou\Dev;

use Ddup\Part\Libs\Str;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Test\PaymentTest;

class PayTest extends PaymentTest
{

    public function test_fuyou()
    {

        try {

            $param = [
                'total_amount' => 20,
                'amount'       => 20,
                'order_no'     => Str::rand(20),
                'subject'      => '描述',
            ];

            $result = $this->app->create('fuyou',
                [
                    'pem_key'    => self::pem_key,
                    'app_id'     => '08A9999999',
                    'mch_id'     => '0002900F0370542',
                    'notify_url' => 'http://test.modernmasters.com/index.php/Supplier/User/myResources.html',
                ]
            )->pay('dy_qr_wechat', new PayOrderStruct($param));

        } catch (\Exception $exception) {
            $this->assertEquals('富友通道报错：未找到路由:100001', $exception->getMessage());
        }
    }
}