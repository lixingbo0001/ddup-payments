<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Config;

use Ddup\Part\Struct\StructReadable;


class RefundOrderStruct extends StructReadable
{
    public $amount;
    public $refund_amount;
    public $order_no;
    public $refund_no;
    public $transaction_id;
    public $channel_refund_id;
}