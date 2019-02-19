<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/23
 * Time: 下午4:37
 */

namespace Ddup\Payments\Test\Fuyou;

use Ddup\Payments\Fuyou\Kernel\Support;
use Ddup\Payments\Test\PaymentTest;

class SignTest extends PaymentTest
{

    public function test_sign2()
    {

        $data               = array();
        $data['ins_cd']     = "08A9999999";
        $data['mchnt_cd']   = "0002900F0370542";
        $data['goods_des']  = "描述";
        $data['order_type'] = "WECHAT";
        $data['order_amt']  = "2000";
        $data['notify_url'] = "http://test.modernmasters.com/index.php/Supplier/User/myResources.html";

        $data['addn_inf']       = "";
        $data['curr_type']      = "CNY";
        $data['term_id']        = "88888888";
        $data['goods_detail']   = "";
        $data['goods_tag']      = "";
        $data['version']        = "1";
        $data['random_str']     = 1548236604;
        $data['mchnt_order_no'] = 1548236604;
        $data['term_ip']        = "117.29.110.187";
        $data['txn_begin_ts']   = '2019012309444';

        $my_sign_str = Support::signString($data);

        $his_sign_str = 'addn_inf=&curr_type=CNY&goods_des=描述&goods_detail=&goods_tag=&ins_cd=08A9999999&mchnt_cd=0002900F0370542&mchnt_order_no=1548236604&notify_url=http://test.modernmasters.com/index.php/Supplier/User/myResources.html&order_amt=2000&order_type=WECHAT&random_str=1548236604&term_id=88888888&term_ip=117.29.110.187&txn_begin_ts=2019012309444&version=1';

        $this->assertEquals($his_sign_str, $my_sign_str);

        $his_sign = 'TE0MAbePqHfOFHZasznnAOYtgtwA3dWGcHQ8zNKeEhcYCvVQ/HCSIiJpzLT1kXp+kpw25mnSxgBya7G55trRuT3vhXiMg6USsjCAlA7X7uam6qW8mpVxUtpk0nDs52fjH2wpeWQLyD9sTb5alWgZIrrnj6NUMAenJp2ITt79IVI=';

        $my_sign = Support::sign($data, self::pem_key);

        $this->assertEquals($his_sign, $my_sign);
    }
}