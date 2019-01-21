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

    public $ssl_verify = false;
    public $mode;
    public $key;
    public $ssl_cert;
    public $cert_key;
    public $rootca;
    public $app_id;
    public $mch_id;
    public $notify_url;
    public $sub_mch_id;
    public $sub_app_id;
}