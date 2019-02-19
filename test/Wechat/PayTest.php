<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/23
 * Time: 下午4:37
 */

namespace Ddup\Payments\Test\Wechat;

use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Test\PaymentTest;

class PayTest extends PaymentTest
{

    public function test_wechatPay()
    {
        try {
            $param = new PayOrderStruct([
                "total_fee" => 0.01,
                'order_no'  => 'tewtadassdfk2233223'
            ]);

            $this->app->create('wechat', ['key' => 'test_key'])->pay('micro', $param);

        } catch (\Exception $exception) {

            $this->assertEquals('Get Wechat API Error:mch_id参数格式错误', $exception->getMessage());
        }

        $this->assertTrue(true);
    }

}