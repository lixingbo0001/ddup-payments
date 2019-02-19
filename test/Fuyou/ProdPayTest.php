<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/23
 * Time: 下午4:37
 */

namespace Ddup\Payments\Test\Fuyou;

use Ddup\Part\Libs\OutCli;
use Ddup\Part\Libs\Str;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Test\PaymentTest;

class ProdPayTest extends PaymentTest
{

    public function test_fuyou()
    {
        try {

            $param = [
                'total_amount' => 1,
                'amount'       => 1,
                'order_no'     => 1211400 . Str::rand(20, range(0, 9)),
                'subject'      => '描述',
                'openid'       => 'oyqy4w1wdTuMWx3ZPMmQdh8qTEO0'
            ];

            $order = $this->app->create('fuyou',
                [
                    'mode'       => 'PROD',
                    'pem_key'    => self::pem_key_prod,
                    'app_id'     => '08M0026086',
                    'mch_id'     => '0003430F1912766',
                    'notify_url' => 'http://test.modernmasters.com/index.php/Supplier/User/myResources.html',
                ]
            )->pay('jsWechat', new PayOrderStruct($param));

            $this->assertNotNull($order->qr_code, 'qr_code不为空');

        } catch (\Exception $exception) {
            $this->assertEquals('fuyou API Error:授权码异常', $exception->getMessage());
        }
    }
}