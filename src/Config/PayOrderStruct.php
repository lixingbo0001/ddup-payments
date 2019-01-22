<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Config;

use Ddup\Part\Struct\StructReadable;


class PayOrderStruct extends StructReadable
{
    public $amount;
    public $order_no;
    public $subject;
    public $openid;
    public $expired_at;
}