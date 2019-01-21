<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Config;

use Ddup\Part\Struct\StructReadable;


class PaymentNotifyStruct extends StructReadable
{
    const success = 'SUCCESS';
    const refund  = 'REFUND';
    const cacel   = 'CANCEL';
    const fail    = 'FAIL';

    public $status;
    public $amount;
    public $transaction_id;
}