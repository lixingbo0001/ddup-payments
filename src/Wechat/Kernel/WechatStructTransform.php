<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Wechat\Kernel;

use Ddup\Part\Struct\StructReadable;
use Ddup\Part\Struct\TransformAble;
use Ddup\Payments\Config\PaymentNotifyStruct;
use Illuminate\Support\Collection;


class WechatStructTransform implements TransformAble
{
    function transform(StructReadable &$struct, Collection $data)
    {
        if ($struct instanceof PaymentNotifyStruct) {
            $struct->status         = $data->get('result_code');
            $struct->amount         = $data->get('total_fee');
            $struct->transaction_id = $data->get('transaction_id');
        }
    }

}