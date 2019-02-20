<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/23
 * Time: 下午4:37
 */

namespace Ddup\Payments\Test\Fuyou\Prod;

use Ddup\Part\Libs\OutCli;
use Ddup\Part\Libs\Str;
use Ddup\Payments\Config\PayOrderStruct;
use Ddup\Payments\Test\PaymentTest;

class DyQrPayTest extends PaymentTest
{

    public function test_fuyou()
    {
        $param = [
            'total_amount' => 0.01,
            'amount'       => 0.01,
            'order_no'     => 1211400 . Str::rand(20, range(0, 9)),
            'subject'      => '描述',
        ];

        $order = $this->app->create('fuyou',
            [
                'mode'       => 'PROD',
                'pem_key'    => self::pem_key_prod,
                'app_id'     => '08M0026086',
                'mch_id'     => '0003430F1742979',
                'notify_url' => 'http://test.modernmasters.com/index.php/Supplier/User/myResources.html',
            ]
        )->pay('dy_qr_wechat', new PayOrderStruct($param));

        OutCli::printLn($order->toArray());

        $this->assertNotEmpty($order->qr_code, 'qr_code不为空');
    }
}