<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/9/30
 * Time: 下午3:54
 */

namespace Ddup\Payments\Test\Upay2;


use Ddup\Part\Libs\Str;
use Ddup\Part\Libs\Time;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Test\PaymentTest;

class GetQrcodeTest extends PaymentTest
{

    public function test_get()
    {
        try {
            $param = [
                'total_amount' => "1",
                'amount'       => "1",
                'order_no'     => Str::rand(30),
                'subject'      => '收卷机',
                'created_at'   => Time::date(null, 'Y-m-d')
            ];

            $result = $this->app->create('upay2',
                [
                    'mode'        => 'test',
                    'key'         => 'fcAmtnx7MwismjWNhNKdHC44mNXtnEQeJkRrhKJwyrW2ysRR',
                    'merchant_id' => '898340149000005',
                    'terminal_id' => '88880001',
                ]
            )->pay('jsWechat', new PayOrderStruct($param));

            $this->logger()->alert($result->qr_code);

            $this->assertNotEmpty($result->qr_code, "二维码获取成功");

        } catch (\Exception $exception) {

            $this->assertEquals('银联通道报错：授权码异常', $exception->getMessage());
        }

    }
}