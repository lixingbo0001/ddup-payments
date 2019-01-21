<?php namespace Tests\Pay;

use Ddup\Part\Libs\OutCli;
use Ddup\Payments\Helper\Application;
use Ddup\Payments\Test\TestCase;
use Illuminate\Support\Collection;


class PaymentTest extends TestCase
{

    public function test_wechatPay()
    {

        try {
            $param = new Collection(["total_fee" => 0.01, 'order_no' => 'tewtadassdfk2233223']);
            $obj   = Application::payment('wechat', ['key' => 'test_key'])->pay('micro', $param);
        } catch (\Exception $exception) {

            $this->assertEquals('Get Wechat API Error:mch_id参数格式错误', $exception->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_upayPay()
    {

        try {
            $param = ["pay" => 0, "id" => null, "empty" => "", 'total_amount' => 0.01, 'amount' => 0.01, 'order_no' => 'tewtadassdfk2233223', 'auth_code' => 'this is mook auth_code', 'subject' => '收卷机'];

            $payment = Application::payment('upay',
                [
                    'key'         => 'EFA89FBFAC6940B4BAB9970E7A4B0E41',
                    'merchant_id' => '898331189990591',
                    'terminal_id' => '11324800'

                ]
            )->pay('microWechat', new Collection($param));
        } catch (\Exception $exception) {
            $this->assertEquals('Upay API Error:授权码异常', $exception->getMessage());
        }
    }

    public function test_fuyou()
    {

        try {
            $param = ["pay" => 0, "id" => null, "empty" => "", 'total_amount' => 0.01, 'amount' => 0.01, 'order_no' => 'tewtadassdfk2233223', 'auth_code' => 'this is mook auth_code', 'subject' => '收款机'];

            $payment = Application::payment('fuyou',
                [
                    'key'         => 'EFA89FBFAC6940B4BAB9970E7A4B0E41',
                    'merchant_id' => '898331189990591',
                    'terminal_id' => '11324800'
                ]
            )->pay('microWechat', new Collection($param));
        } catch (\Exception $exception) {
            $this->assertEquals('fuyou API Error:授权码异常', $exception->getMessage());
        }
    }

}