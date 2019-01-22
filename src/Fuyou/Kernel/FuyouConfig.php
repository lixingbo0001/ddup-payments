<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/21
 * Time: 下午9:22
 */

namespace Ddup\Payments\Fuyou\Kernel;


use Ddup\Part\Struct\StructReadable;

class FuyouConfig extends StructReadable
{
    const MODE_TEST = 'test';
    const MODE_PROD = 'prod';


    public $ssl_verify    = false;
    public $mode;
    public $pem_key;
    public $app_id;
    public $mch_id;
    public $notify_url;
    public $expire_minute = 5;
    public $sub_mch_id;
    public $sub_app_id;
    public $version       = '1.0';
}