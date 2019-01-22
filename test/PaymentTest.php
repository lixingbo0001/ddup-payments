<?php namespace Tests\Pay;

use Ddup\Part\Libs\OutCli;
use Ddup\Part\Libs\Str;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Helper\Application;
use Ddup\Payments\Test\TestCase;


class PaymentTest extends TestCase
{

    const pem_key = __DIR__ . '/Providers/file/keypem.pem';

    public function test_wechatPay()
    {

        try {
            $param = new PayOrderStruct([
                "total_fee" => 0.01,
                'order_no'  => 'tewtadassdfk2233223'
            ]);

            Application::payment('wechat', ['key' => 'test_key'])->pay('micro', $param);

        } catch (\Exception $exception) {

            $this->assertEquals('Get Wechat API Error:mch_id参数格式错误', $exception->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_upayPay()
    {

        try {
            $param = [
                "pay"          => 0,
                "id"           => null,
                "empty"        => "",
                'total_amount' => 0.01,
                'amount'       => 0.01,
                'order_no'     => Str::rand(20),
                'auth_code'    => 'this is mook auth_code',
                'subject'      => '收卷机'
            ];

            Application::payment('upay',
                [
                    'key'         => 'EFA89FBFAC6940B4BAB9970E7A4B0E41',
                    'merchant_id' => '898331189990591',
                    'terminal_id' => '11324800'

                ]
            )->pay('microWechat', new PayOrderStruct($param));

        } catch (\Exception $exception) {
            $this->assertEquals('银联通道报错：授权码异常', $exception->getMessage());
        }
    }

    public function test_fuyou()
    {
        try {

            $param = [
                'total_amount' => 0.01,
                'amount'       => 0.01,
                'order_no'     => Str::rand(20),
                'subject'      => '收款机',
            ];

            Application::payment('fuyou',
                [
                    'pem_key' => self::pem_key,
                    'app_id'  => '08A9999999',
                    'mch_id'  => '0002900F0370542',
                ]
            )->pay('jsAli', new PayOrderStruct($param));

        } catch (\Exception $exception) {
            $this->assertEquals('fuyou API Error:授权码异常', $exception->getMessage());
        }
    }

}