<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Upay2\Kernel;

use Ddup\Part\Struct\StructReadable;
use Ddup\Part\Struct\TransformAble;
use Ddup\Payments\Config\PaymentNotifyStruct;
use Illuminate\Support\Collection;


class UpayStructTransform implements TransformAble
{
    function transform(StructReadable &$struct, Collection $data)
    {
        $billPayment = $data->get('billPayment');
        $billPayment = is_string($billPayment) ? json_decode($data->get('billPayment')) : $billPayment;
        $data        = new Collection($billPayment);

        if ($struct instanceof PaymentNotifyStruct) {

            switch ($data->get('status')) {
                case "NEW_ORDER":
                    $struct->status     = PaymentNotifyStruct::pending;
                    $struct->status_msg = '待付款';
                    break;
                case "WAIT_BUYER_PAY":
                    $struct->status     = PaymentNotifyStruct::pending;
                    $struct->status_msg = '等待用户付款';
                    break;
                case "TRADE_SUCCESS":
                    $struct->status     = PaymentNotifyStruct::success;
                    $struct->status_msg = '支付成功';
                    break;
                case "TRADE_CLOSED":
                    $struct->status     = PaymentNotifyStruct::fail;
                    $struct->status_msg = '交易已关闭';
                    break;
                case "TRADE_REFUND":
                    $struct->status     = PaymentNotifyStruct::cacel;
                    $struct->status_msg = '已退款';
                    break;
                case "UNKNOWN":
                    $struct->status     = PaymentNotifyStruct::fail;
                    $struct->status_msg = '不明确的交易状态';
                    break;
                default:
                    $struct->status     = PaymentNotifyStruct::fail;
                    $struct->status_msg = '支付失败';
                    break;
            }

            $struct->total_amount = $data->get('totalAmount');
            $struct->amount       = $data->get('buyerPayAmount');
            $struct->wx_appid     = $data->get('buyerId', '');

            if ($data->get('trade_no')) {
                $struct->transaction_id = $data->get('trade_no');
            }
        }
    }

}

