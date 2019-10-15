<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/9/30
 * Time: 下午3:54
 */

namespace Ddup\Payments\Test\Upay2;


use Ddup\Payments\Test\TestCase;
use Ddup\Payments\Upay2\Kernel\Support;

class SignTest extends TestCase
{

    public function test_sign()
    {
        $key    = 'fcAmtnx7MwismjWNhNKdHC44mNXtnEQeJkRrhKJwyrW2ysRR';
        $expect = '57F81BAF8E3BAE1190B26D6C733038AF';
        $str    = '{
            "walletOption": "SINGLE",
            "billNo": "31940000201700002",
            "billDate": "2017-06-26",
            "sign": "2631915B7F7822C4B00A488A32E03764",
            "requestTimestamp": "2017-06-26 17:28:02", 
            "instMid": "QRPAYDEFAULT",
            "msgSrc": "WWW.TEST.COM",
            "totalAmount": "1",
            "goods": [{
                "body": "微信二维码测试",
                "price": "1",
                "goodsName": "微信二维码测试",
                "goodsId": "1",
                "quantity": "1",
                "goodsCategory": "TEST"
            }],
            "msgType": "bills.getQRCode", 
            "mid": "898340149000005", 
            "tid": "88880001"
        }';

        $data = json_decode($str, true);

        $this->assertEquals($expect, Support::generateSign($data, $key));
    }
}