<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Upay\Kernel;

use Ddup\Part\Struct\StructReadable;
use Ddup\Part\Struct\TransformAble;
use Ddup\Payments\Config\PaymentNotifyStruct;
use Illuminate\Support\Collection;


class UpayStructTransform implements TransformAble
{
    function transform(StructReadable &$struct, Collection $data)
    {
        if ($struct instanceof PaymentNotifyStruct) {

            switch ($data->get('trade_status')) {
                case "STARTUP":
                    $struct->status     = PaymentNotifyStruct::pending;
                    $struct->status_msg = '待付款';
                    break;
                case "TRADE_SUCCESS":
                    $struct->status     = PaymentNotifyStruct::success;
                    $struct->status_msg = '支付成功';
                    break;
                case "TRADE_FAILED":
                    $struct->status     = PaymentNotifyStruct::fail;
                    $struct->status_msg = '交易失败';
                    break;
                case "TRADE_CLOSED":
                    $struct->status     = PaymentNotifyStruct::fail;
                    $struct->status_msg = '交易已关闭';
                    break;
                case "TRADE_CANCELED":
                    $struct->status     = PaymentNotifyStruct::cacel;
                    $struct->status_msg = '已取消';
                    break;
                case "TRADE_WAITING_PAY":
                    $struct->status     = PaymentNotifyStruct::pending;
                    $struct->status_msg = '用户未支付';
                    break;
                default:
                    $struct->status     = PaymentNotifyStruct::fail;
                    $struct->status_msg = '支付失败';
                    break;
            }

            $struct->total_amount = $data->get('total_amount');
            $struct->amount       = $data->get('pay_amount');
            $struct->wx_appid     = $data->get('wx_appid', '');

            if ($data->get('trade_no')) {
                $struct->transaction_id = $data->get('trade_no');
            }
        }
    }

}

