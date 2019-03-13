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

class JsPayTest extends PaymentTest
{

    public function test_wechat()
    {
        $param = [
            'total_amount' => 0.01,
            'amount'       => 0.01,
            'order_no'     => 1211400 . Str::rand(20, range(0, 9)),
            'subject'      => '描述',
            'openid'       => 'oyqy4w9pBmbzzEFDG66ciWr7Sufs',
        ];

        $result = $this->app->create('fuyou',
            [
                'mode'       => 'PROD',
                'pem_key'    => self::pem_key_prod,
                'app_id'     => '08M0026086',
                'mch_id'     => '0003430F1742979',
                'notify_url' => 'http://test.modernmasters.com/index.php/Supplier/User/myResources.html',
            ]
        )->pay('js_wechat', new PayOrderStruct($param));

        OutCli::printLn($result->toArray());

        $this->assertNotEmpty($result->sdk, 'sdk参数不为空');
    }

    public function test_ali()
    {
        $param = [
            'total_amount' => 0.01,
            'amount'       => 0.01,
            'order_no'     => 1211400 . Str::rand(20, range(0, 9)),
            'subject'      => '描述',
            'openid'       => '999',
        ];

        try {

            $result = $this->app->create('fuyou',
                [
                    'mode'       => 'PROD',
                    'pem_key'    => self::pem_key_prod,
                    'app_id'     => '08M0026244',
                    'mch_id'     => '0003430F2164030',
                    'notify_url' => 'http://test.modernmasters.com/index.php/Supplier/User/myResources.html',
                ]
            )->pay('js_ali', new PayOrderStruct($param));

            OutCli::printLn($result->toArray());

            $this->assertNotEmpty($result->sdk, 'sdk参数不为空');

        } catch (\Exception $exception) {

            $this->assertEquals('富友通道报错：买家不存在:010002', $exception->getMessage(), '支付宝用户ID错误');
        }

    }
}