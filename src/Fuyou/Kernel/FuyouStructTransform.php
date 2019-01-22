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

            switch ($data->get('trans_stat')) {
                case 'SUCCESS':
                    $struct->status     = PaymentNotifyStruct::success;
                    $struct->status_msg = '支付成功';
                    break;
                case 'REFUND':
                    $struct->status     = PaymentNotifyStruct::refund;
                    $struct->status_msg = '转入退款';
                    break;
                case 'NOTPAY':
                    $struct->status     = PaymentNotifyStruct::pending;
                    $struct->status_msg = '待支付';
                    break;
                case 'CLOSED':
                    $struct->status     = PaymentNotifyStruct::fail;
                    $struct->status_msg = '已关闭';
                    break;
                case 'REVOKED':
                    $struct->status     = PaymentNotifyStruct::fail;
                    $struct->status_msg = '已撤销';
                    break;
                case 'USERPAYING':
                    $struct->status     = PaymentNotifyStruct::pending;
                    $struct->status_msg = '支付中';
                    break;
                case 'PAYERROR':
                    $struct->status     = PaymentNotifyStruct::fail;
                    $struct->status_msg = '其他原因支付失败';
                    break;
            }
        }
    }

}