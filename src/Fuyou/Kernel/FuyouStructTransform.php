<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Fuyou\Kernel;

use Ddup\Part\Struct\StructReadable;
use Ddup\Part\Struct\TransformAble;
use Ddup\Payments\Config\PaymentNotifyStruct;
use Illuminate\Support\Collection;


class FuyouStructTransform implements TransformAble
{
    function transform(StructReadable &$struct, Collection $data)
    {
        if ($struct instanceof PaymentNotifyStruct) {
            $struct->total_amount   = $data->get('order_amt');
            $struct->amount         = $data->get('order_amt');
            $struct->transaction_id = $data->get('transaction_id');

            switch ($data->get('result_code')) {
                case '000000':
                    $struct->status     = PaymentNotifyStruct::success;
                    $struct->status_msg = '支付成功';
                    break;
                case '030011':
                    $struct->status     = PaymentNotifyStruct::refund;
                    $struct->status_msg = '已经退款';
                    break;
                case '030006':
                    $struct->status     = PaymentNotifyStruct::cacel;
                    $struct->status_msg = '已取消';
                    break;
                case '030007':
                    $struct->status     = PaymentNotifyStruct::fail;
                    $struct->status_msg = '已关闭';
                    break;
                case '030010':
                    $struct->status     = PaymentNotifyStruct::pending;
                    $struct->status_msg = '支付中';
                    break;
            }
        }
    }

}