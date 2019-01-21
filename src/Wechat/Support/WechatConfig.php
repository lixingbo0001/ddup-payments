<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/9/30
 * Time: 下午6:14
 */

namespace Ddup\Payments\Wechat\Support;

use Ddup\Part\Struct\StructReadable;


class WechatConfig extends StructReadable
{
    const MODE_NORMAL  = 'normal'; // 普通模式
    const MODE_DEV     = 'dev'; // 沙箱模式
    const MODE_SERVICE = 'service'; // 服务商

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