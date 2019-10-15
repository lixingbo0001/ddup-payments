<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Upay2\Kernel;

use Ddup\Part\Struct\StructReadable;


class UpayConfig extends StructReadable
{
    const MODE_TEST = 'test';
    const MODE_PROD = 'prod';

    public $ssl_verify = false;
    public $mode;
    public $key;
    public $sign_type  = 'MD5';
    public $terminal_id;
    public $merchant_id;
    public $notify_url;
    public $msg_src;

}