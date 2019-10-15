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
use Ddup\Payments\Test\PaymentTest;
use Ddup\Payments\Test\Upay2\Support\Client;
use Ddup\Payments\Upay2\Kernel\Support;

class ClientGetQrcodeTest extends PaymentTest
{

    public function test_get()
    {
        $client = new Client();

        $data = [
            "billNo"           => Str::rand(28),
            "billDate"         => Time::date(null, 'Y-m-d'),
            "requestTimestamp" => Time::date(),
            "instMid"          => "QRPAYDEFAULT",
            "msgSrc"           => "WWW.TEST.COM",
            "totalAmount"      => "2",
            "msgType"          => "bills.getQRCode",
            "mid"              => "898340149000005",
            "tid"              => "88880001"
        ];


        $data['sign'] = Support::generateSign($data, 'fcAmtnx7MwismjWNhNKdHC44mNXtnEQeJkRrhKJwyrW2ysRR');

        dump($data);

        $result = $client->json('', $data);

        dump($result);

        $this->assertNotEmpty($result['billQRCode'], '二维码查询成功');
    }
}