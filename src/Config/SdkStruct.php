<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Config;

use Ddup\Part\Struct\StructReadable;


class SdkStruct extends StructReadable
{
    public $appId;
    public $timeStamp;
    public $nonceStr;
    public $package;
    public $signType = 'MD5';
    public $paySign;
    public $transaction_id;
}